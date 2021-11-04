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
      'version' => '1.0.3',
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
    $url = $this->config->urls($this)."RockIframe.css";
    $this->style = "<link type='text/css' href='$url' rel='stylesheet'>";

    $this->addHookAfter("ProcessPageView::execute", $this, "addIframe");
    $this->addHookAfter("/rockiframeleaflet/", $this, "renderLeaflet");
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

  /**
   * Show image file in a leaflet map viewer
   * @return void
   */
  public function leaflet($img, $options = []) {
    $opt = $this->wire(new WireData()); /** @var WireData $opt */
    $opt->setArray([
      'x' => 1000,
      'y' => 1000,
      'minZoom' => 0,
      'maxZoom' => 12,
      'zoom' => 0,
    ]);
    $opt->setArray($options);

    $optstr = '';
    foreach($opt->getArray() as $k=>$v) $optstr .= "&$k=$v";

    $config = $this->wire->config;
    $img = str_replace($config->paths->root, $config->urls->root, $img);
    $this->frame = "<iframe src='/rockiframeleaflet/?img=$img{$optstr}' class='RockIframe'></iframe>";
  }

  /**
   * Render the leaflet viewer
   * @return string
   */
  public function renderLeaflet(HookEvent $event) {
    // add a runtime flag to the page object
    // this is for apps where all frontend requests are redirected to the backend
    // checking for this page property makes it possible to prevent redirect
    $page = $event->wire->page;
    $page->rockiframeleaflet = true;
    $page->title = 'RockIframe Leaflet Viewer';

    $img = $this->wire->input->get('img', 'string');
    $file = $this->wire->config->paths->root.ltrim($img, "/");
    if(is_file($file)) $img .= "?m=".filemtime($file);

    $path = $this->wire->config->paths($this);
    return $this->wire->files->render($path."leaflet/viewer.php", [
      'img' => $img,
      'x' => $this->wire->input->get('x', 'int'),
      'y' => $this->wire->input->get('y', 'int'),
      'minZoom' => $this->wire->input->get('minZoom', 'int'),
      'maxZoom' => $this->wire->input->get('maxZoom', 'int'),
      'zoom' => $this->wire->input->get('zoom', 'int'),
    ]);
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
