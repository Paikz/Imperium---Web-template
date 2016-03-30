<?php

class CImage {

  private $src;
  private $verbose;
  private $saveAs;
  private $quality;
  private $ignoreCache;
  private $newWidth;
  private $newHeight;
  private $cropToFit;
  private $sharpen;
  private $pathToImage;
  private $fileExtension;
  private $image;
  private $imgInfo;
  private $cacheFileName;

  private $cropWidth;
  private $cropHeight;
  private $maxWidth = 2000;
  private $maxHeight = 2000;

  function __construct() {
    $this->displayImage($this->verbose);
  }

  function errorMessage($message) {
    header("Status: 404 Not Found");
    die('img.php says 404 - ' . '{$this->pathToImage}' . htmlentities($message));
  }

  function verbose($message) {
    echo "<p>" . htmlentities($message) . "</p>";
  }

  function initErrorReporting() {
    error_reporting(-1);              // Report all type of errors
    ini_set('display_errors', 1);     // Display all errors
    ini_set('output_buffering', 0);   // Do not buffer outputs, write directly

    define('IMG_PATH', __DIR__ . '/../../../');
    define('CACHE_PATH', __DIR__ . '/../../../cache/');
  }

  function validateIncomingVariables() {
    //
    // Validate incoming arguments
    //
    is_null($this->newWidth) or (is_numeric($this->newWidth) and $this->newWidth > 0 and $this->newWidth <= $this->maxWidth) or $this->errorMessage('Width out of range');
    is_null($this->newHeight) or (is_numeric($this->newHeight) and $this->newHeight > 0 and $this->newHeight <= $this->maxHeight) or $this->errorMessage('Height out of range');
    is_dir(IMG_PATH) or $this->errorMessage('The image dir is not a valid directory.');
    is_writable(CACHE_PATH) or $this->errorMessage('The cache dir is not a writable directory.');
    isset($this->src) or $this->errorMessage('Must set src-attribute.');
    preg_match('#^[a-z0-9A-Z-_\.\/]+$#', $this->src) or $this->errorMessage('Filename contains invalid characters.');
    //substr_compare(IMG_PATH, $this->pathToImage, 0, strlen(IMG_PATH)) == 0 or $this->errorMessage('Security constraint: Source image is not directly below the directory IMG_PATH.');
    is_null($this->saveAs) or in_array($this->saveAs, array('png', 'jpg', 'jpeg')) or $this->errorMessage('Not a valid extension to save image as');
    is_null($this->quality) or (is_numeric($this->quality) and $this->quality > 0 and $this->quality <= 100) or $this->errorMessage('Quality out of range');
    is_null($this->cropToFit) or ($this->cropToFit and $this->newWidth and $this->newHeight) or $this->errorMessage('Crop to fit needs both width and height to work');
  }

  function getVariables() {
    //
    // Get the incoming arguments
    //
    $this->src        = isset($_GET['src'])     ? $_GET['src']      : null;
    $this->verbose    = isset($_GET['verbose']) ? true              : null;
    $this->saveAs     = isset($_GET['save-as']) ? $_GET['save-as']  : null;
    $this->quality    = isset($_GET['quality']) ? $_GET['quality']  : 60;
    $this->ignoreCache = isset($_GET['no-cache']) ? true           : null;
    $this->newWidth   = isset($_GET['width'])   ? $_GET['width']    : null;
    $this->newHeight  = isset($_GET['height'])  ? $_GET['height']   : null;
    $this->cropToFit  = isset($_GET['crop-to-fit']) ? true : null;
    $this->sharpen    = isset($_GET['sharpen']) ? true : null;
    $this->pathToImage = realpath(IMG_PATH . $this->src);
    $this->validateIncomingVariables();
  }

  function displayVerbose($verbose) {
    //
    // Start displaying log if verbose mode & create url to current image
    //
    if($verbose) {
      $query = array();
      parse_str($_SERVER['QUERY_STRING'], $query);
      unset($query['verbose']);
      $url = '?' . http_build_query($query);


      echo <<<EOD
    <html lang='en'>
    <meta charset='UTF-8'/>
    <title>img.php verbose mode</title>
    <h1>Verbose mode</h1>
    <p><a href=$url><code>$url</code></a><br>
    <img src='{$url}' /></p>
EOD;
    }
  }

  function outputImage($file, $verbose) {
    $info = getimagesize($file);
    !empty($info) or $this->errorMessage("The file doesn't seem to be an image.");
    $mime   = $info['mime'];

    $lastModified = filemtime($file);
    $gmdate = gmdate("D, d M Y H:i:s", $lastModified);

    if($verbose) {
      $this->verbose("Memory peak: " . round(memory_get_peak_usage() /1024/1024) . "M");
      $this->verbose("Memory limit: " . ini_get('memory_limit'));
      $this->verbose("Time is {$gmdate} GMT.");
    }

    if(!$verbose) header('Last-Modified: ' . $gmdate . ' GMT');
    if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified){
      if($verbose) { $this->verbose("Would send header 304 Not Modified, but its verbose mode."); exit; }
      header('HTTP/1.0 304 Not Modified');
    } else {
      if($verbose) { $this->verbose("Would send header to deliver image with modified time: {$gmdate} GMT, but its verbose mode."); exit; }
      header('Content-type: ' . $mime);
      readfile($file);
    }
    exit;
  }

  function sharpenImage($image) {
    $matrix = array(
      array(-1,-1,-1,),
      array(-1,16,-1,),
      array(-1,-1,-1,)
    );
    $divisor = 8;
    $offset = 0;
    imageconvolution($image, $matrix, $divisor, $offset);
    return $image;
  }

  function getInfoOnImage($verbose) {
    //
    // Get information on the image
    //
    $this->imgInfo = list($width, $height, $type, $attr) = getimagesize($this->pathToImage);
    !empty($this->imgInfo) or $this->errorMessage("The file doesn't seem to be an image.");
    $mime = $this->imgInfo['mime'];

    if($verbose) {
      $filesize = filesize($this->pathToImage);
      $this->verbose("Image file: {$this->pathToImage}");
      $this->verbose("Image information: " . print_r($this->imgInfo, true));
      $this->verbose("Image width x height (type): {$width} x {$height} ({$type}).");
      $this->verbose("Image file size: {$filesize} bytes.");
      $this->verbose("Image mime type: {$mime}.");
    }
  }

  function calculateDimensions($verbose) {
    //
    // Calculate new width and height for the image
    //
    $width = $this->imgInfo[0];
    $height = $this->imgInfo[1];
    $aspectRatio = $width / $height;

    if($this->cropToFit && $this->newWidth && $this->newHeight) {
      $targetRatio = $this->newWidth / $this->newHeight;
      $this->cropWidth   = $targetRatio > $aspectRatio ? $width : round($height * $targetRatio);
      $this->cropHeight  = $targetRatio > $aspectRatio ? round($width  / $targetRatio) : $height;
      if($verbose) { $this->verbose("Crop to fit into box of {$this->newWidth}x{$this->newHeight}. Cropping dimensions: {$this->cropWidth}x{$this->cropHeight}."); }
    }
    else if($this->newWidth && !$this->newHeight) {
      $this->newHeight = round($this->newWidth / $aspectRatio);
      if($verbose) { $this->verbose("New width is known {$this->newWidth}, height is calculated to {$this->newHeight}."); }
    }
    else if(!$this->newWidth && $this->newHeight) {
      $this->newWidth = round($this->newHeight * $aspectRatio);
      if($verbose) { $this->verbose("New height is known {$this->newHeight}, width is calculated to {$this->newWidth}."); }
    }
    else if($this->newWidth && $this->newHeight) {
      $ratioWidth  = $width  / $this->newWidth;
      $ratioHeight = $height / $this->newHeight;
      $ratio = ($ratioWidth > $ratioHeight) ? $ratioWidth : $ratioHeight;
      $this->newWidth  = round($width  / $ratio);
      $this->newHeight = round($height / $ratio);
      if($verbose) { $this->verbose("New width & height is requested, keeping aspect ratio results in {$this->newWidth}x{$this->newHeight}."); }
    }
    else {
      $this->newWidth = $width;
      $this->newHeight = $height;
      if($verbose) { $this->verbose("Keeping original width & heigth."); }
    }
  }

  function createCache($verbose) {
    //
    // Creating a filename for the cache
    //
    $parts          = pathinfo($this->pathToImage);
    $this->fileExtension  = $parts['extension'];
    $this->saveAs         = is_null($this->saveAs) ? $this->fileExtension : $this->saveAs;
    $quality_       = is_null($this->quality) ? null : "_q{$this->quality}";
    $cropToFit_     = is_null($this->cropToFit) ? null : "_cf";
    $sharpen_       = is_null($this->sharpen) ? null : "_s";
    $dirName        = preg_replace('/\//', '-', dirname($this->src));
    $this->cacheFileName = CACHE_PATH . "-{$dirName}-{$parts['filename']}_{$this->newWidth}_{$this->newHeight}{$quality_}{$cropToFit_}{$sharpen_}.{$this->saveAs}";
    $this->cacheFileName = preg_replace('/^a-zA-Z0-9\.-_/', '', $this->cacheFileName);

    if($verbose) { $this->verbose("Cache file is: {$this->cacheFileName}"); }

    //
    // Is there already a valid image in the cache directory, then use it and exit
    //
    $imageModifiedTime = filemtime($this->pathToImage);
    $cacheModifiedTime = is_file($this->cacheFileName) ? filemtime($this->cacheFileName) : null;

    // If cached image is valid, output it.
    if(!$this->ignoreCache && is_file($this->cacheFileName) && $imageModifiedTime < $cacheModifiedTime) {
      if($verbose) { $this->verbose("Cache file is valid, output it."); }
      $this->outputImage($this->cacheFileName, $verbose);
    }

    if($verbose) { $this->verbose("Cache is not valid, process image and create a cached version of it."); }
  }


  function openOriginalImage($verbose) {
    //
    // Open up the original image from file
    //
    if($verbose) { $this->verbose("File extension is: {$this->fileExtension}"); }

    switch($this->fileExtension) {
      case 'jpg':
      case 'jpeg':
        $this->image = imagecreatefromjpeg($this->pathToImage);
        if($verbose) { $this->verbose("Opened the image as a JPEG image."); }
        break;

      case 'png':
        $this->image = imagecreatefrompng($this->pathToImage);
        if($verbose) { $this->verbose("Opened the image as a PNG image."); }
        break;

      default: $this->errorMessage('No support for this file extension.');
    }

    //
    // Resize the image if needed
    //
    $width = $this->imgInfo[0];
    $height = $this->imgInfo[1];

    if($this->cropToFit) {
      if($verbose) { $this->verbose("Resizing, crop to fit."); }
      $cropX = round(($width - $this->cropWidth) / 2);
      $cropY = round(($height - $this->cropHeight) / 2);
      $imageResized = imagecreatetruecolor($this->newWidth, $this->newHeight);
      imagecopyresampled($imageResized, $this->image, 0, 0, $cropX, $cropY, $this->newWidth, $this->newHeight, $this->cropWidth, $this->cropHeight);
      $this->image = $imageResized;
      $width = $this->newWidth;
      $height = $this->newHeight;
    }
    else if(!($this->newWidth == $width && $this->newHeight == $height)) {
      if($verbose) { $this->verbose("Resizing, new height and/or width."); }
      $imageResized = imagecreatetruecolor($this->newWidth, $this->newHeight);
      imagecopyresampled($imageResized, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $width, $height);
      $this->image  = $imageResized;
      $width  = $this->newWidth;
      $height = $this->newHeight;
    }
    //
    // Apply filters and postprocessing of image
    //
    if($this->sharpen) {
      $this->image = $this->sharpenImage($this->image);
    }
  }

  function saveAndOutputImage($verbose) {
    //
    // Save the image
    //
    switch($this->saveAs) {
      case 'jpeg':
      case 'jpg':
        if($verbose) { $this->verbose("Saving image as JPEG to cache using quality = {$this->quality}."); }
        imagejpeg($this->image, $this->cacheFileName, $this->quality);
      break;

      case 'png':
        if($verbose) { $this->verbose("Saving image as PNG to cache."); }
        imagepng($this->image, $this->cacheFileName);
      break;

      default:
        $this->errorMessage('No support to save as this file extension.');
      break;
    }

    if($verbose) {
      clearstatcache();
      $filesize = filesize($this->pathToImage);
      $cacheFilesize = filesize($this->cacheFileName);
      $this->verbose("File size of cached file: {$cacheFilesize} bytes.");
      $this->verbose("Cache file has a file size of " . round($cacheFilesize/$filesize*100) . "% of the original size.");
    }

    //
    // Output the resulting image
    //
    $this->outputImage($this->cacheFileName, $verbose);
    }

  function displayImage($verbose) {
    $this->initErrorReporting();
    $this->getVariables();
    $this->displayVerbose($this->verbose);
    $this->getInfoOnImage($this->verbose);
    $this->calculateDimensions($this->verbose);
    $this->createCache($this->verbose);
    $this->openOriginalImage($this->verbose);
    $this->saveAndOutputImage($this->verbose);
    }

}
