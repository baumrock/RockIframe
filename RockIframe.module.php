<?php

namespace ProcessWire;

/**
 * Iframe Sidebar for the ProcessWire page edit screen
 *
 * @author Bernhard Baumrock, 03.03.2021
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class RockIframe extends WireData implements Module, ConfigurableModule
{
  private $frame;

  public static function getModuleInfo()
  {
    return [
      'title' => 'RockIframe',
      'version' => '1.0.4',
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

  public function init()
  {
    $url = $this->config->urls($this) . "RockIframe.css";
    $this->style = "<link type='text/css' href='$url' rel='stylesheet'>";

    $this->addHookAfter("ProcessPageView::execute", $this, "addIframe");
    $this->addHookAfter("/rockiframeleaflet/", $this, "renderLeaflet");
    $this->addHookAfter("/rockiframeimage/", $this, "renderImage");
  }

  public function addIframe(HookEvent $event)
  {
    if (!$this->frame) return;
    $html = str_replace("</body>", $this->frame . "</body>", $event->return);
    $html = str_replace("</head>", $this->style . "</head>", $html);
    $event->return = $html;
  }

  public function getUrl($data)
  {
    $config = $this->wire->config;
    if ($this->wire->files->fileInPath($data, $config->paths->root) and is_file($data)) {
      $url = str_replace($config->paths->root, $config->urls->root, $data);
      return $url . "?m=" . filemtime($data);
    }
    if ($data instanceof Pagefile) {
      $file = $data;
      if (!$file->filemtime) return;
      return $file->url . "?m=" . $file->filemtime;
    }
    if ($data instanceof Pagefiles) {
      if (!$data->count()) return;
      $file = $data->first();
      if (!$file->filemtime) return;
      return $file->url . "?m=" . $file->filemtime;
    }
    return $data;
  }

  /**
   * Show image file in a leaflet map viewer
   * @return void
   */
  public function leaflet($img, $options = [])
  {
    $opt = $this->wire(new WireData());
    /** @var WireData $opt */
    $opt->setArray([
      'x' => 1000,
      'y' => 1000,
      'minZoom' => 0,
      'maxZoom' => 12,
      'zoom' => 0,
      'raw' => false,
      'ts' => false, // cache busting timestamp
    ]);
    $opt->setArray($options);

    $optstr = '';
    foreach ($opt->getArray() as $k => $v) {
      if ($k == 'raw') continue;
      $optstr .= "&$k=$v";
    }

    $config = $this->wire->config;
    $img = str_replace($config->paths->root, $config->urls->root, $img);
    $markup = "<iframe src='/rockiframeleaflet/?img=$img{$optstr}' class='RockIframe'></iframe>";

    if ($opt->raw) return $markup;

    $this->frame = $markup;
  }

  /**
   * Show image file in iframe
   * @return void
   */
  public function image($img, $options = [])
  {
    if ($img instanceof Pagefiles) $img = $img->first();
    if (!$img instanceof Pagefile) return false;
    $images = ['jpg', 'jpeg', 'gif', 'png', 'webp'];
    if (in_array($img->ext, $images)) {
      $this->frame = "<iframe src='/rockiframeimage/?img={$img->url}' class='RockIframe'></iframe>";
    } else $this->show($img);
  }

  /**
   * Render the leaflet viewer
   * @return string
   */
  public function renderLeaflet(HookEvent $event)
  {
    // add a runtime flag to the page object
    // this is for apps where all frontend requests are redirected to the backend
    // checking for this page property makes it possible to prevent redirect
    $page = $event->wire->page;
    $page->rockiframeleaflet = true;
    $page->title = 'RockIframe Leaflet Viewer';

    $img = $this->wire->input->get('img', 'string');
    $file = $this->wire->config->paths->root . ltrim($img, "/");
    if (is_file($file)) $img .= "?m=" . filemtime($file);

    $path = $this->wire->config->paths($this);
    return $this->wire->files->render($path . "leaflet/viewer.php", [
      'img' => $img,
      'x' => $this->wire->input->get('x', 'int'),
      'y' => $this->wire->input->get('y', 'int'),
      'minZoom' => $this->wire->input->get('minZoom', 'int'),
      'maxZoom' => $this->wire->input->get('maxZoom', 'int'),
      'zoom' => $this->wire->input->get('zoom', 'int'),
    ]);
  }

  public function renderImage(HookEvent $event)
  {
    $img = $this->wire->input->get('img', 'string');
    return "<html><head><style>
      img{max-width:100%}
      html,body{padding:0;margin:0;}
      </style><body>
      <img src=$img>
      </body></html>";
  }

  public function show($data)
  {
    $url = $this->getUrl($data);
    if (!$url) return;
    $this->frame = "<iframe src='$url' class='RockIframe'></iframe>";
  }

  public function showUrl($url)
  {
    $this->frame = "<iframe src='$url' class='RockIframe'></iframe>";
  }

  /**
   * Config inputfields
   * @param InputfieldWrapper $inputfields
   */
  public function getModuleConfigInputfields($inputfields)
  {
    $name = strtolower($this);
    $inputfields->add([
      'type' => 'markup',
      'label' => 'Documentation & Updates',
      'icon' => 'life-ring',
      'value' => "<p>Hey there, coding rockstars! 👋</p>
        <ul>
          <li><a class=uk-text-bold href=https://www.baumrock.com/modules/$name/docs>Read the docs</a> and level up your coding game! 🚀💻😎</li>
          <li><a class=uk-text-bold href=https://www.baumrock.com/rock-monthly>Sign up now for our monthly newsletter</a> and receive the latest updates and exclusive offers right to your inbox! 🚀💻📫</li>
          <li><a class=uk-text-bold href=https://github.com/baumrock/$name>Show some love by starring the project</a> and keep me motivated to build more awesome stuff for you! 🌟💻😊</li>
          <li><a class=uk-text-bold href=https://paypal.me/baumrockcom>Support my work with a donation</a>, and together, we'll keep rocking the coding world! 💖💻💰</li>
        </ul>",
    ]);
    return $inputfields;
  }
}
