<?php
/**
 * Created by PhpStorm.
 * User: binhnx
 * Date: 4/24/19
 * Time: 8:30 PM
 */

namespace Herode\ARepo\Console;

use Herode\ARepo\Exceptions\FileAlreadyExistsException;
use Herode\ARepo\Generators\MigrationGenerator;
use Herode\ARepo\Generators\ModelGenerator;
use Herode\ARepo\Generators\RepositoryGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeRepositoryCommand extends Command
{

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'make:arepository';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new repository.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * @var Collection
     */
    protected $generators = null;


    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(){
        $this->laravel->call([$this, 'fire'], func_get_args());
    }

    /**
     * Execute the command.
     *
     * @return bool
     */
    public function fire()
    {
        $this->generators = new Collection();

        $migrationGenerator = new MigrationGenerator([
            'name'   => 'create_' . snake_case(str_plural($this->argument('name'))) . '_table',
            'fields' => $this->option('fillable'),
            'force'  => $this->option('force'),
        ]);

        if (!$this->option('skip-migration')) {
            $this->generators->push($migrationGenerator);
        }

        $modelGenerator = new ModelGenerator([
            'name'     => $this->argument('name'),
            'fillable' => $this->option('fillable'),
            'force'    => $this->option('force')
        ]);

        if (!$this->option('skip-model')) {
            $this->generators->push($modelGenerator);
        }

        foreach ($this->generators as $generator) {
            $generator->run();
        }

        $model = $modelGenerator->getRootNamespace() . '\\' . $modelGenerator->getName();
        $model = str_replace([
            "\\",
            '/'
        ], '\\', $model);

        try {
            (new RepositoryGenerator([
                'name'      => $this->argument('name'),
                'rules'     => $this->option('rules'),
                'force'     => $this->option('force'),
                'model'     => $model
            ]))->run();
            $this->info("Repository created successfully.");

            return true;
        } catch (FileAlreadyExistsException $e) {
            $this->error($this->type . ' already exists!');

            return false;
        }
    }


    /**
     * The array of command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'The name of class being generated.',
                null
            ],
        ];
    }


    /**
     * The array of command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            [
                'fillable',
                null,
                InputOption::VALUE_OPTIONAL,
                'The fillable attributes.',
                null
            ],
            [
                'rules',
                null,
                InputOption::VALUE_OPTIONAL,
                'The rules of validation attributes.',
                null
            ],
            [
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the creation if file already exists.',
                null
            ],
            [
                'skip-migration',
                null,
                InputOption::VALUE_NONE,
                'Skip the creation of a migration file.',
                null,
            ],
            [
                'skip-model',
                null,
                InputOption::VALUE_NONE,
                'Skip the creation of a model.',
                null,
            ],
        ];
    }
}
