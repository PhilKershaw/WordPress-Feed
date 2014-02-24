<?php
require_once 'Exception.php';
/**
 * WordPress Feed class
 * Aids with the fetching and rendering of a WordPress Feed.
 *
 * @author Phil Kershaw
 * @copyright Wilson Cooke
 */
class Wordpress_Feed
{
    /**
     * SimpleXML Object of the WordPress feed
     * @var object
     */
    private $_feed = null;
    /**
     * Post template
     * @var string
     */
    private $_title_template   = '<span class="subheader">%s</span>';
    private $_post_template    = '<p>%s</p>';
    private $_time_template    = '<small>%s</small>';
    private $_wrapper_template = '<div class="wp-post">%s</div>';

    public function __construct($feedUri = null)
    {
        if (null == $feedUri) throw new WordPress_Feed_Exception('No feed uri specified.');

        $this->_feed = simplexml_load_file($feedUri);
    }

    public function render()
    {
        return $this->_formatPosts();
    }
    
    private function _formatPosts()
    {
        if (null == $this->_feed) throw new WordPress_Feed_Exception('Bad XML or feed is empty.');

        $html = '';
        foreach ($this->_feed->channel->item as $item) {

            $content  = $this->_format_title($item->title)."\n\t";
            $content .= $this->_format_time($item->pubDate)."\n\t\t";
            $content .= $this->_format_post($item->description);

            $html .= sprintf(
                $this->_wrapper_template,
                $content
            );
        }
        return $html;
    }

    private function _format_title($title)
    {
        return sprintf(
            $this->_title_template,
            $title
        );
    }

    private function _format_time($time)
    {
        $time = date(
            'jS F Y',
            strtotime($time)
        );
        return sprintf(
            $this->_time_template,
            $time
        );
    }

    private function _format_post($post)
    {
        return sprintf(
            $this->_post_template,
            trim($post)
        );
    }
}
