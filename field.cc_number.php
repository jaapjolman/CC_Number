<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PyroStreams Encrypt Field Type
 *
 * @package		PyroCMS\Core\Modules\Streams Core\Field Types
 * @author		Parse19
 * @copyright	Copyright (c) 2011 - 2012, Parse19
 * @license		http://parse19.com/pyrostreams/docs/license
 * @link		http://parse19.com/pyrostreams
 */
class Field_cc_number
{
	public $field_type_slug			= 'cc_number';
	
	public $db_col_type				= 'blob';

	public $version					= '1.1';

	public $author					= array('name'=>'AI Web Systems, Inc.', 'url'=>'http://aiwebsystems.com');
	
	// --------------------------------------------------------------------------

	/**
	 * Process before saving to database
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	public function pre_save($input, $field, $stream, $row_id)
	{
		$this->CI->load->library('encrypt');

		// Is this an existing row?
		if ( $row_id != NULL )
		{
			
			// Are there Xs?
			if ( strpos($input, 'XXXX-XXXX-XXXX-') !== false)
			{

				// Yes, return the row_id value
				return $this->CI->db->select($field->field_slug)->where('id', $row_id)->limit(1)->get($stream->stream_prefix.$stream->stream_slug)->row(0)->{$field->field_slug};
			}
		}

		return $this->CI->encrypt->encode(preg_replace('/\D/', '', $input)) . ':' . substr($input, -4);
	}

	// --------------------------------------------------------------------------

	/**
	 * Process before outputting
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	public function pre_output($input)
	{
		$this->CI->load->library('encrypt');
		
		//Debug
		//return $this->CI->encrypt->decode($input);
		
		return 'XXXX-XXXX-XXXX-'.substr($this->CI->encrypt->decode(substr($input, 0, -5)), -4);
	}

	// --------------------------------------------------------------------------

	/**
	 * Output form input
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	public function form_output($params)
	{
		$this->CI->load->library('encrypt');

		$options['name'] 	= $params['form_slug'];
		$options['id']		= $params['form_slug'];

		// If we have post data and are returning form
		// values (because of most likely a form validation error),
		// we will just have the posted plain text value
		$options['value'] = ($_POST) ? 'XXXX-XXXX-XXXX-'.substr(substr($params['value'], 0, -5), -4) : (!empty($params['value'])) ? 'XXXX-XXXX-XXXX-'.substr($this->CI->encrypt->decode(substr($params['value'], 0, -5)), -4) : '';
		
		return form_input($options);
	}
}