<?php 

namespace TrackingCodeInstaller;

class Injector {

	public $id;

	public $track;

	public function __construct( $id, $track )
	{

		$this->id = $id;
		$this->track = $track;
		
		switch ( sanitize_key( $track[ 'position_in_html' ] ) ) {
			case 'head':
				$hook = 'wp_head';
				break;
			
			case 'head_top':
				$hook = 'wp_enqueue_scripts';
				break;
			
			default:
				$hook = 'wp_footer';
				break;
		}

		$priority = absint( $this->id );
		$priority = $priority > 0 ? $priority : 10;

		add_action( $hook, array( $this, 'inject' ), $priority ); 

	}

	public function inject()
	{
		$code = $this->track['code'];
		
		$has_tag = ( stripos('<script', $code) === false && stripos('</script>', $code) === false );

		$code = sprintf( 
			PHP_EOL . '%s' . $code . '%s <!-- %s%s -->' . PHP_EOL . PHP_EOL, 
			( ! $has_tag ? '<script>' : '' ),
			( ! $has_tag ? '</script>' : '' ),
			$this->track['label'],
			( $this->track['label'] !== $this->id ? ':'. $this->id : '' )
		);

		echo $code;

	}

}