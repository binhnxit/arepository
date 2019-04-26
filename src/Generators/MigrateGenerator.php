<?php
/**
 * Created by PhpStorm.
 * User: binhnx
 * Date: 4/24/19
 * Time: 8:52 PM
 */

namespace Herode\ARepo\Generators;

use Herode\ARepo\Generators\Migrations\NameParser;
use Herode\ARepo\Generators\Migrations\SchemaParser;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Class MigrationGenerator
 * @package Prettus\Repository\Generators
 */
class MigrationGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'migration/plain';


    /**
     * Get base path of destination file.
     *
     * @return string
     */
    public function getBasePath()
    {
        return base_path() . '/database/migrations/';
    }


    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . $this->getFileName() . '.php';
    }


    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return '';
    }


    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return '';
    }


    /**
     * Get migration name.
     *
     * @return string
     */
    public function getMigrationName()
    {
        return strtolower($this->name);
    }


    /**
     * Get file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return date('Y_m_d_His_') . $this->getMigrationName();
    }


    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser()
    {
        return new SchemaParser($this->fields);
    }


    /**
     * Get name parser.
     *
     * @return NameParser
     */
    public function getNameParser()
    {
        return new NameParser($this->name);
    }


    /**
     * Get stub templates.
     *
     * @return string
     */
    public function getStub()
    {
        $parser = $this->getNameParser();

        $file = 'migration_create';
        $replacements = [
            'class'  => $this->getClass(),
            'table'  => $parser->getTable(),
            'fields' => $this->getSchemaParser()->up(),
        ];

        $path = config('arepository.generator.stubsOverridePath', __DIR__);

        if (!file_exists($path . "/Stubs/{$file}.stub")) {
            $path = __DIR__;
        }

        if (!file_exists($path . "/Stubs/{$file}.stub")) {
            throw new FileNotFoundException($path . "/Stubs/migration/{$file}.stub");
        }

        return Stub::create($path . "/Stubs/{$file}.stub", $replacements);
    }
}
