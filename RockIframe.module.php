<?php namespace ProcessWire;
/**
 * Iframe Sidebar for the ProcessWire page edit screen
 *
 * @author Bernhard Baumrock, 03.03.2021
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class RockIframe extends WireData implements Module {
  private $frame;

  public static function getModuleInfo() {
    return [
      'title' => 'RockIframe',
      'version' => '1.0.1',
      'summary' => 'Iframe Sidebar for the ProcessWire page edit screen',
      // must be true, not template=admin!
      // otherwise the pageview hook will not fire
      'autoload' => true,
      'singular' => true,
      'icon' => 'columns',
      'requires' => [],
      'installs' => [],
    ];
  }

  public function init() {
    $this->addHookAfter("ProcessPageView::execute", $this, "addIframe");
    $url = $this->config->urls($this)."RockIframe.css";
    $this->style = "<link type='text/css' href='$url' rel='stylesheet'>";
  }

  public function addIframe(HookEvent $event) {
    if(!$this->frame) return;
    $html = str_replace("</body>", $this->frame."</body>", $event->return);
    $html = str_replace("</head>", $this->style."</head>", $html);
    $event->return = $html;
  }

  public function getUrl($data) {
    $config = $this->wire->config;
    if(is_file($data)) {
      $url = str_replace($config->paths->root, $config->urls->root, $data);
      return $url."?m=".filemtime($data);
    }
    if($data instanceof Pagefile) {
      $file = $data;
      if(!$file->filemtime) return;
      return $file->url."?m=".$file->filemtime;
    }
    if($data instanceof Pagefiles) {
      if(!$data->count()) return;
      $file = $data->first();
      if(!$file->filemtime) return;
      return $file->url."?m=".$file->filemtime;
    }
    return $data;
  }

  public function show($data) {
    $url = $this->getUrl($data);
    if(!$url) return;
    $this->frame = "<iframe src='$url' class='RockIframe'></iframe>";
  }

  public function showUrl($url) {
    $this->frame = "<iframe src='$url' class='RockIframe'></iframe>";
  }

}
