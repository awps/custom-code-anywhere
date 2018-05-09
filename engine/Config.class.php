<?php

namespace TrackingCodeInstaller;

class Config
{
    protected $title;
    protected $page_slug;
    protected $nonceAction;

    public function __construct()
    {
        $this->title = \tc_installer_config('plugin_name');
        $this->page_slug = \tc_installer_config('id');
        $this->nonceAction = 'tracking-code-installer-admin-page-nonce-token';

        \add_action('admin_menu', [$this, 'register']);
        \add_action('admin_enqueue_scripts', [$this, 'adminEnqueue'], 99);
        \add_action('wp_ajax_tracking_code_installer', [$this, 'processAjaxRequest'], 99);
    }

    /*
    -------------------------------------------------------------------------------
    Register the page and add it to admin menu
    -------------------------------------------------------------------------------
    */
    public function register()
    {
        add_options_page(
            __('Tracking Codes', 'tracking-code-installer'),
            __('Tracking Codes', 'tracking-code-installer'),
            'manage_options',
            $this->page_slug,
            [$this, 'render']
        );
    }

    /*
    -------------------------------------------------------------------------------
    Adding the scripts and styles
    -------------------------------------------------------------------------------
    */
    public function adminEnqueue()
    {
        if (is_admin() && !empty($_GET['page']) && $this->page_slug === $_GET['page']) {
            wp_enqueue_style('tc-installer-styles-admin');
            wp_enqueue_script('tc-installer-config-admin');
        }
    }

    /*
    -------------------------------------------------------------------------------
    Catch the data sent via AJAX and add it to DB
    -------------------------------------------------------------------------------
    */
    public function processAjaxRequest()
    {
        if (is_admin() && current_user_can('activate_plugins') && !empty($_POST)) {
            $obj_data = $_POST;

            if (!empty($obj_data)) {
                if (empty($obj_data['is_delete'])) {
                    parse_str($obj_data['form'], $data);

                    // Nonce valid save data.
                    if (wp_verify_nonce($data['tci-security-token'], $this->nonceAction)) {
                        // get saved data from DB
                        $saved_data = get_option('tc_installer_data', []);

                        // Adding a new code
                        if (empty($data['tci-id'])) {
                            $id = sanitize_key(uniqid(time(), true));
                        } // Updating an existing code
                        else {
                            $id = sanitize_key($data['tci-id']);
                        }

                        $data['code'] = trim(wp_unslash($data['code']));
                        $data['label'] = trim($data['label']);
                        $data['label'] = !empty($data['label']) ? $data['label'] : $id;

                        // Save to DB
                        unset($data['tci-id']);
                        unset($data['tci-security-token']);
                        $saved_data[$id] = $data;

                        // Done
                        update_option('tc_installer_data', $saved_data);

                        // Return some info back to browser
                        die(json_encode(
                            [
                                'id' => $id,
                                'status' => 'success',
                                'msg' => __('A new code has been added successfuly.', 'tracking-code-installer'),
                                'html' => $this->_singleItemFromList($id, $data),
                            ]
                        ));
                    }
                } else {
                    if (wp_verify_nonce($obj_data['nonce'], $this->nonceAction)) {
                        // get saved data from DB
                        $saved_data = get_option('tc_installer_data', []);

                        unset($saved_data[$obj_data['id']]);

                        // Done
                        update_option('tc_installer_data', $saved_data);

                        // Return some info back to browser
                        die(json_encode(
                            [
                                'is_delete' => 1,
                                'status' => 'success',
                            ]
                        ));
                    }
                }
            }
        }

        die(json_encode(
            [
                'status' => 'fail',
                'msg' => __('Could not save data. Try again.', 'tracking-code-installer'),
            ]
        ));
    }

    /*
    -------------------------------------------------------------------------------
    Create the page HTML
    -------------------------------------------------------------------------------
    */
    public function render()
    {
        echo sprintf(
            '<div class="wrap">
				<h1>' . $this->title . '</h1>
				<div class="tci-connect %s">
					<div class="tci-panel-left">
					' . $this->_list() . '
					</div>
					<div class="tci-panel-right">
						<form id="tci-form">
							' . $this->_fields() . '
							<div class="tci-submit wp-clearfix">
								<input type="submit" name="submit" id="submit" class="button button-primary" value="%s">
								<span id="cancel-edit" class="cancel-edit button button-link">%s</span>
								<span id="submit-spinner" class="spinner"></span>
								<span id="tc-delete" class="tc-delete button button-link tc-hidden">%s</span>
							</div>
							<input type="hidden" name="tci-id" value="" />
							<input type="hidden" name="tci-security-token" value="%s" />
						</form>
					</div>
				</div>
			</div>',
            \sanitize_html_class($this->page_slug),
            __('Save', 'tracking-code-installer'),
            __('Cancel', 'tracking-code-installer'),
            __('Delete', 'tracking-code-installer'),
            \wp_create_nonce($this->nonceAction)
        );
    }

    protected function _list()
    {
        $opt = get_option('tc_installer_data', []);
        $output = '';

        if (!empty($opt)) {
            foreach ($opt as $opt_id => $value) {
                $output .= $this->_singleItemFromList($opt_id, $value);
            }
        }

        return $output;
    }

    protected function _singleItemFromList($opt_id, $value)
    {
        $value = wp_parse_args($value, [
            'code' => '',
            'label' => '',
            'position_in_html' => '',
            'priority' => '',
        ]);

        return sprintf(
            '<div id="tc-%s" data-id="%1$s" class="tc-single-item wp-clearfix">
				<div class="tc-show-label">%s <span>%4$s: %5$s</span></div>
				<div class="tc-label">%2$s</div>
				<div class="tc-code">%s</div>
				<div class="tc-position_in_html">%s</div>
				<div class="tc-priority">%s</div>
				<div class="tc-edit-item">%s</div>
			</div>',
            esc_attr($opt_id),
            $value['label'],
            esc_html($value['code']),
            esc_html($value['position_in_html']),
            esc_html($value['priority']),
            __('Edit', 'tracking-code-installer')
        );
    }

    protected function _fields()
    {
        return sprintf(
            '<div class="tci-fields-group">
				<div class="tci-field-code">
					<div class="title">%2$s</div>
					<textarea name="code"></textarea>
				</div>
				<div class="tci-field-label">
					<div class="title">%3$s</div>
					<input type="text" name="label" value="" />
				</div>
				<div class="tci-field-position_in_html">
					<div class="title">%4$s</div>
					<label> <input type="radio" name="position_in_html" value="head">%5$s</label>
					<label> <input type="radio" name="position_in_html" value="footer" checked="checked">%6$s</label>
					<label> <input type="radio" name="position_in_html" value="head_top">%7$s</label>
				</div>
				<div class="tci-field-priority">
					<div class="title">%8$s</div>
					<input type="number" name="priority" value="10" min="1" />
				</div>
			</div>',
            '',
            __('Tracking code(or plain JS)', 'tracking-code-installer'),
            __('Label', 'tracking-code-installer'),
            __('Position in HTML', 'tracking-code-installer'),
            __('Header', 'tracking-code-installer'),
            __('Footer', 'tracking-code-installer'),
            __('Header top', 'tracking-code-installer'),
            __('Priority', 'tracking-code-installer')
        );
    }
}
