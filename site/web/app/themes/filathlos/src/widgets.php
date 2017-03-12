<?php

// Register our widgets

function filathlos_widgets_init()
{
    register_widget('SocialMediaWidget');
}
add_action('widgets_init', 'filathlos_widgets_init');


class SocialMediaWidget extends WP_Widget
{
  function __construct()
  {
    $widget_ops = array(
      'classname' => 'social-media-widget', 
      'description' => 'Displays social urls meda with icons' );
      parent::__construct( 'social-media-text', __( 'Social Media Widget' ), $widget_ops );
      $this->alt_option_name = 'social-media-widget';
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'facebook' => '', 'twitter' => '', 'linkedin' => '') );
    $facebook = $instance['facebook'];
    $twitter = $instance['twitter'];
    $linkedin = $instance['linkedin'];
  ?>
  <label for="<?php echo $this->get_field_id('facebook'); ?>">Facebook url :
  <input type="text" class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" value="<?php echo esc_attr($facebook); ?>">
  </label>
  <p><label for="<?php echo $this->get_field_id('twitter'); ?>">Twitter url:
  <input type="text" class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" value="<?php echo esc_attr($twitter); ?>">
  </label></p>
  <p><label for="<?php echo $this->get_field_id('linkedin'); ?>">LinkedIn url:
  <input type="text" class="widefat" id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" value="<?php echo esc_attr($linkedin); ?>">
  </label></p>

  <?php
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['facebook'] = $new_instance['facebook'];
    $instance['twitter'] = $new_instance['twitter'];
    $instance['linkedin'] = $new_instance['linkedin'];
    return $instance;
  }

  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
    echo $before_widget;
    $facebook = empty($instance['facebook']) ? ' ' : apply_filters('widget_text', $instance['facebook']);
    $twitter = empty($instance['twitter']) ? ' ' : apply_filters('widget_text', $instance['twitter']);
    $linkedin = empty($instance['linkedin']) ? ' ' : apply_filters('widget_text', $instance['linkedin']);
    // WIDGET CODE GOES HERE
    echo '<div class="box">
    <div class="round">
    <div class="con"><ul>';
    echo '<li><a href="'.$facebook. '"><i class="fa fa-facebook"></i></a></li>';
    echo '<li><a href="'.$twitter. '"><i class="fa fa-twitter"></i></a></li>';
    echo '<li><a href="'.$linkedin. '"><i class="fa fa-linkedin"></i></a></li>';
    echo ' </div>
    </div>
    </ul></div>';
    echo $after_widget;
  }
}