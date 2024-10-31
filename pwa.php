<?php
$this_is_main_file = !count(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

include("pwa_config.php");


class PWA_static extends lum_pwa_config
{
  static function current_user_unique()
  {
    if (isset($_COOKIE[self::$pwa_unique_get_key])) {
      return $_COOKIE[self::$pwa_unique_get_key];
    } else {
      return false;
    }
  }
}
class PWA
{
  public $manifest;

  public $pwa_lib;
  public function __construct($manifest)
  {
    $manifest = json_decode($manifest);
    $this->manifest = $manifest->manifest;
    $this->pwa_lib = $manifest->pwa_lib;
  }

  private function generate_random_string($length = 10)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
  }
  public function set_unique($key = 0)
  {
    if ($key == 0) {
      $key = $this->generate_random_string(15);
    }
    setcookie(PWA_static::$pwa_unique_get_key, $key, time() + (60 * 60 * 24 * 30));
    return $key;
  }

  public function set_property($property, $value, $entity = "manifest")
  {
    $this[$entity][$property] = $value;
  }

  public function include_manifest()
  {
    $url = $this->pwa_lib->lib_url;


    $manifest = urlencode(base64_encode(json_encode($this->manifest)));
    echo "<link rel='manifest' href='$url?manifest=$manifest'/>";
  }

  public function include_script()
  {
    $url = $this->pwa_lib->lib_url . '?serviceWorkerInit=' . urlencode(base64_encode(file_get_contents($this->pwa_lib->service_worker)));
    $url .= '&files=' . urlencode(base64_encode(json_encode($this->pwa_lib->cache_files)));
    $url .= '&cacheVersion=' . urlencode(base64_encode($this->pwa_lib->cache_version));
    $url .= '&script';

    echo "<script src='$url'></script>";
  }
}




if ($this_is_main_file) {
  if (isset($_GET['manifest']) && $_GET['manifest'] != '') {
    header('Content-Type: application/json');
    $manifest = base64_decode($_GET['manifest']);
    echo $manifest;
  }

  if (isset($_GET['script']) && isset($_GET['cacheVersion']) && isset($_GET['serviceWorkerInit']) && isset($_GET['files']) && $_GET['cacheVersion'] != '' && $_GET['files'] != '' && $_GET['serviceWorkerInit'] != '') {
    header('Content-Type: application/javascript');
    $hook = $_SERVER['SCRIPT_NAME'] . '?pwa_installed_hook';
    if(!!PWA_static::current_user_unique()){
      $hook .= '&key=' . PWA_static::current_user_unique();
    }
    $service_worker_init = $_GET['serviceWorkerInit'];
    $files = $_GET['files'];

    $url = $_SERVER['SCRIPT_NAME'] . '?serviceWorker=' . urlencode($service_worker_init) . '&files=' . urlencode($files) . '&cacheVersion=' . urlencode($_GET['cacheVersion']);
    $main_code = str_replace("%url%", $url, PWA_static::$pwa_script_file);
    $main_code = str_replace("%install_function%", PWA_static::$install_pwa_function_name, $main_code);
    $main_code = str_replace("%hook%", $hook, $main_code);
    echo $main_code;

  }

  if (isset($_GET['serviceWorker']) && isset($_GET['cacheVersion']) && isset($_GET['files']) && $_GET['files'] != '' && $_GET['serviceWorker'] != '' && $_GET['cacheVersion'] != '') {
    header('Content-Type: application/javascript');
    $service_worker = base64_decode($_GET['serviceWorker']);
    $cache_version = base64_decode($_GET['cacheVersion']);
    $files_json = base64_decode($_GET['files']);
    $main_code = str_replace("%files_json%", $files_json, str_replace("%cache_version%", $cache_version, PWA_static::$pwa_service_worker_code)) . $service_worker;
    echo $main_code;
  }

  if (isset($_GET['pwa_installed_hook'])){
    if (isset($_GET['key'])){
      PWA_static::pwa_installed_hook($_GET['key']);
    }
    else {
      PWA_static::pwa_installed_hook();
    }
  }
}

