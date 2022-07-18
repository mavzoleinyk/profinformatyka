<?php 
/**
Template Page for the gallery overview

Follow variables are useable :

  $gallery     : Contain all about the gallery
  $images      : Contain all images, path, title
  $pagination  : Contain the pagination content

 You can check the content when you insert the tag <?php var_dump($variable) ?>
 If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>
**/
?>

<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.isotope.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/nggallery/gallery_isotope.css">

  <script>
    jQuery(function($){
      
      var $container = $('#isotopegallery');
      
      $container.isotope({
        itemSelector: '.photo'
      });
      
      $('#filters a').click(function(){
  var selector = $(this).attr('data-filter');
  $container.isotope({ filter: selector });
  return false;
});
    });
  </script>
<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><?php if (!empty ($gallery)) : ?>
 
<div id="isotopegallery" class="photos clearfix" id="<?php echo $gallery->anchor ?>">
 
        <?php
                //Used to break down and extract the width and height of each image
                function get_string_between($string, $start, $end){
                        $string = " ".$string;
                        $ini = strpos($string,$start);
                        if ($ini == 0) return "";
                        $ini += strlen($start);
                        $len = strpos($string,$end,$ini) - $ini;
                        return substr($string,$ini,$len);
                }
        ?>
 
        <!-- Thumbnails -->
        <?php foreach ( $images as $image ) : ?>
               
               
                <?php if ( !$image->hidden ) {
                        //GET the Size parameters for each image. this i used to size the div box that the images goes inside of.
                        $the_size_string = $image->size;
                        $thewidth = get_string_between($the_size_string, "width=\"", "\"");
                        $theheight = get_string_between($the_size_string, "height=\"", "\"");
                        $divstyle = 'width:'.$thewidth.'px; height:'.$theheight.'px;'; 
                }?>
               
 
                        <?php
                                //Get the TAGS for this image  
                                $tags = wp_get_object_terms($image->pid,'ngg_tag');
                                $tag_string = ''; //store the list of strings to be put into the class menu for isotpe filtering       
                                ?>
                                <?php foreach ( $tags as $tag ) : ?>     
                                  <?php $tag_string = $tag_string.$tag->slug.' ';  //alternativley can use $tag->name;, slug with put hyphen between words ?>      
                                <?php endforeach; ?>   
                                               
                <div class="photo <?php echo $tag_string ?>" style="<?php echo $divstyle; ?>">
                        <a href="<?php echo $image->imageURL ?>" title="<?php echo $image->description ?>" <?php echo $image->thumbcode ?> >
                                <?php if ( !$image->hidden ) { ?>
                                <img title="<?php echo $image->alttext ?>" alt="<?php echo $image->alttext ?>" src="<?php echo $image->thumbnailURL ?>" />
                                <?php } ?>
                        </a>
                </div> 
       
                <?php if ( $image->hidden ) continue; ?>
                <?php if ( $gallery->columns > 0 && ++$i % $gallery->columns == 0 ) { ?>
                        <br style="clear: both" />
                <?php } ?>
 
        <?php endforeach; ?>
       
        <!-- Pagination -->
        <?php echo $pagination ?>
       
</div>
 
<?php endif; ?>