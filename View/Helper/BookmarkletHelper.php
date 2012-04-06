<?php
App::uses('AppHelper', 'View/Helper');
/**
 * # BookmarkletHelper #
 * 
 * This is a simple CakePHP helper to add a "Post it!" bookmarklet in your view.
 * You know, those nice javascript you can add to the browser's bookmarks bar;
 * the user can submit a selected portion of any webpage, along with the original title and url
 * to an action in your cakephp app (to save it, pre-fill a form, and so on)
 * 
 * version 0.1.0
 * 
 * requirements: PHP5 / CakePhp 2.0
 * (should be easy to convert it to cakephp 1.3)
 * 
 * http://stefanomanfredini.info/2012/04/cakephp-2-simple-bookmarklet-helper/
 * 
 * MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * 
 *  
 */
class BookmarkletHelper extends AppHelper {
    public $helpers = array('Html','Form');

    public $html_version;
    
    protected $_defaultOptions = array(
       'title' => 'drag this to your browser\'s bookmarks bar',
       'text'  => 'Post it!',
       'class' => null,
       'destination' => array('controller' => 'posts','action' => 'bookmarklet'),
       
       'html_version' => true
    );
    
    protected $_actualOptions = array();
    
    protected $output = '';
    
    /**
     * contructor, sets default options as overridden from Config 
     * @param View $View
     * @param type $settings 
     */
    public function __construct(View $View, $settings = array()) {
        parent::__construct($View, $settings);
        
        $Bookmarklet = (array)Configure::read('Bookmarklet');
        $this->_actualOptions = am($this->_defaultOptions, $Bookmarklet);                      
    }
    
    /**
     * initialize values; overrides default options with those passed from $this->render() 
     * and sets some property needed in other methods
     * @param type $options 
     */
    protected function _initialize($options = array()) {
        
        $this->_actualOptions = am($this->_actualOptions, $options);  
        
        $options = $this->_actualOptions;
        
        if(!is_null($options['html_version'])) {
            $this->html_version = $options['html_version'];
        }       
        
    }
    
    /**
     * main method of the helper. 
     * calls other methods to build the html/javascript output, then prints it.
     * @param type $options 
     */
    public function render($options = array()) {
        
        $this->_initialize($options);
        
        if($this->html_version) {
            $this->output = $this->_renderHtmlVersion($options);
        } else {
            $this->output = $this->_renderPlainTextVersion($options);
        }
        
        $this->output .= $this->_actualOptions['text'] ."</a>";
        
        echo $this->output;
    }

    protected function _renderPlainTextVersion ($options = array()) {
        $url = 'http://'.$_SERVER['SERVER_NAME']. $this->Html->url($this->_actualOptions['destination']);
        $output = "<a href=\"javascript:var intro_text = window.getSelection ? window.getSelection().toString() : (document.selection ? document.selection.createRange().text : \"');location.href='".$url."?link='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title)+'&intro='+encodeURIComponent(intro_text);\" class='". $this->_actualOptions['class']."'  title='".$this->_actualOptions['title']."'>";
        return $output;
        
    }
    
    protected function _renderHtmlVersion ($options = array()) {
        $url = 'http://'.$_SERVER['SERVER_NAME']. $this->Html->url($this->_actualOptions['destination']);   
        $output = "<a href='javascript:(function(){var h=\"\",s,g,c,i;if(window.getSelection){s=window.getSelection();if(s.rangeCount){c=document.createElement(\"div\");for(i=0;i<s.rangeCount;++i){c.appendChild(s.getRangeAt(i).cloneContents());}h=c.innerHTML}}else if((s=document.selection)&&s.type==\"Text\"){h=s.createRange().htmlText;}location.href=\"".$url."?link=\"+encodeURIComponent(location.href)+\"&title=\"+encodeURIComponent(document.title)+\"&intro=\"+encodeURIComponent(h);})()' class='".$this->_actualOptions['class']."'  title='".$this->_actualOptions['title']."'>";
        return $output;
    }
    
}
?>
