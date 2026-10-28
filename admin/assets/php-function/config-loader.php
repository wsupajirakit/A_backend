<?php
function resolveConfigPath($configPath){
  $localPath = preg_replace('/\.ini$/', '.local.ini', $configPath);
  if($localPath !== null && $localPath !== $configPath && file_exists($localPath)){
    return $localPath;
  }
  return $configPath;
}

function loadConfigIni($configPath){
  $targetPath = resolveConfigPath($configPath);
  $conf = parse_ini_file($targetPath, true);
  return $conf === false ? array() : $conf;
}
?>
