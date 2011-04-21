<?php
class SU_File {
	/**
	 * Validate the image
	 *
	 * @access	public
	 * @return	bool
	 */	
	function is_image($mine)
	{
		// IE will sometimes return odd mime-types during upload, so here we just standardize all
		// jpegs or pngs to the same file type.

		$png_mimes  = array('image/x-png');
		$jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');
		
		if (in_array($mine, $png_mimes))
		{
			$mine = 'image/png';
		}
		
		if (in_array($mine, $jpeg_mimes))
		{
			$mine = 'image/jpeg';
		}

		$img_mimes = array(
							'image/gif',
							'image/jpeg',
							'image/png',
						   );

		return (in_array($mine, $img_mimes, TRUE)) ? TRUE : FALSE;
	}
}
?>