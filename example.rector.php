<?php declare(strict_types=1);

use mxr576\RectorDrupalConfigurator\RectorDrupalConfigurator;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
  $drc = new RectorDrupalConfigurator();
  $drc->configure($rectorConfig);
  $rectorConfig->sets([
    LevelSetList::UP_TO_PHP_81,
  ]);

  $rectorConfig->paths([__DIR__ . '/src']);
};
