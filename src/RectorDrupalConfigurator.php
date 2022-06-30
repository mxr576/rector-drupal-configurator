<?php declare(strict_types=1);

namespace mxr576\RectorDrupalConfigurator;

use Composer\Autoload\ClassLoader;
use DrupalFinder\DrupalFinder;
use Rector\Config\RectorConfig;
use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use mglaman\DrupalStaticAutoloader\Autoloader as DrupalAutoloader;

final class RectorDrupalConfigurator {

  public function configure(RectorConfig $config, string $locateDrupalRootFrom = __DIR__): void {
    $drupalFinder = new DrupalFinder();
    // $drupalFinder->getDrupalRoot() can return FALSE, see $drupalFinder->locateRoot().
    if (!$drupalFinder->locateRoot($locateDrupalRootFrom)) {
      throw new \RuntimeException(sprintf('Unable to identify Drupal root from "%s".', $locateDrupalRootFrom));
    }
    $drupalRoot = $drupalFinder->getDrupalRoot();

    // Autoloading Drupal related files is a complex and complicated task,
    // let's leave it to a dedicated library instead of using Rector's built-in
    // solution.
    // @see https://github.com/palantirnet/drupal-rector/blob/fd30e68a5f46fb3f6d860a093e36b3cf2f90a8ba/rector.php#L20
    $drupalAutoloader = DrupalAutoloader::getLoader($drupalRoot);
    $drupalAutoloader->register();

    $this->fixPhpUnitCompatibility($drupalRoot);

    $config->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);
    $config->importNames(TRUE, FALSE);
    $config->importShortClasses(FALSE);
  }

  /**
   * Sightly modified version of https://github.com/mglaman/drupal-static-autoloader/blob/main/drupal-phpunit-hack.php
   *
   * @param string $drupalRoot
   *
   * @return void
   */
  private function fixPhpUnitCompatibility(string $drupalRoot): void {
    $autoloader = require  $drupalRoot . '/autoload.php';
    if (!$autoloader instanceof ClassLoader) {
      return;
    }

    // Inspired by Symfony's simple-phpunit remove typehints from TestCase.
    $alteredFile = $autoloader->findFile('PHPUnit\Framework\TestCase');
    if ($alteredFile === false) {
      return;
    }
    $phpunit_dir = dirname($alteredFile, 3);
    // Mutate TestCase code to make it compatible with Drupal 8 and 9 tests.
    $alteredCode = file_get_contents($alteredFile);
    if ($alteredCode === false) {
      throw new \RuntimeException("Found $alteredFile but could not get its contents to fix return types.");
    }
    $alteredCode = preg_replace('/^    ((?:protected|public)(?: static)? function \w+\(\)): void/m', '    $1', $alteredCode);
    assert($alteredCode !== null);
    $alteredCode = str_replace("__DIR__ . '/../Util/", "'$phpunit_dir/src/Util/", $alteredCode);
    // Only write when necessary.
    $filename = $drupalRoot . '/core/tests/fixtures/TestCase.php';

    if (!file_exists($filename) || md5_file($filename) !== md5($alteredCode)) {
      file_put_contents($filename, $alteredCode);
    }

    // This can be called several times.
    require_once $filename;
  }

}
