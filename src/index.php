<?php
  require __DIR__ . '/../vendor/autoload.php';
  require __DIR__ . '/../bootstrap.php';

  use Script\StationsImport;
  use Script\PlacesImport;

  // StationsImport::run();
  PlacesImport::run();
