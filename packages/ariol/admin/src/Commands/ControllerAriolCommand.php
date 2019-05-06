<?php namespace Ariol\Commands;

use File;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class ControllerAriolCommand extends Command
{
    /**
     * Название и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'ariol:controller {name : Controller name} 
                            {--type=base : Type - base or tab} 
                            {--model= : Model name} 
                            {--table=TableName : Table name} 
                            {--m|migration : Create a new migration file for the model.}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'The creation of a model based on the rules of the admin.';

    /**
     * Создание нового экземпляра команды.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Выполнение консольной команды.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $type = $this->option('type');

        /* Если не задаётся название модели, то она будет называться так, как и контроллер. */
        $model = $this->option('model') != null ? $this->option('model') : $name;

        $table = $this->option('table');

        $types = ['base', 'tab'];

        $pathController = base_path() . '/app/Http/Controllers/Admin/' . $name . 'Controller.php';
        $pathModel = base_path() . '/app/Http/Models/' . $model . '.php';

        if (! in_array($type, $types)) {
            $this->error('There are only 2 types of models: base and tab.');
        } elseif (file_exists($pathController)) {
            $this->error('Controller already exists.');
        } elseif (file_exists($pathModel)) {
            $this->error('Model already exists.');
        } else {
            $model = str_replace('/', '\\', $model);

            /* Подготовка создания контроллера. */
            $contentController = File::get(__DIR__ . '/stubs/controller.stub');
            $contentController = str_replace('ModelName', 'App\Http\Models\\' . $model, $contentController);

            /* Чтобы не создавать два stub (так как отличие будет только в одном слове), производится замена. */
            if ($type == 'tab') {
                $contentController = str_replace('BaseController', 'TabController', $contentController);
            }

            /* Если указывается другой путь для создания контроллера.  */
            $controller = explode('/', $name);

            $count = count($controller);
            if ($count == 1) {
                $contentController = str_replace('NameController', $name . 'Controller', $contentController);
            } else {
                $name = end($controller); // Получаем название контроллера без его пути.
                $contentController = str_replace('NameController', $name . 'Controller', $contentController);

                /* Получаем чисто адрес нового контроллера. */
                unset($controller[$count-1]);

                $controller = implode('/', $controller);

                $dir = base_path() . '/app/Http/Controllers/Admin/' . $controller;
                if (! file_exists($dir)) {
                    File::makeDirectory($dir);
                }

                $controller = str_replace('/', '\\', $controller);
                $contentController = str_replace('Controllers\Admin', 'Controllers\Admin\\' . $controller, $contentController);
            }

            File::put($pathController, $contentController);

            $this->createModel($pathModel, $model);

            if ($this->option('migration') && $table != 'TableName') {
                $this->createMigration();
            }

            $this->info('Created successfully.');
        }
    }

    /**
     * Создание модели.
     *
     * @param $path
     * @param $model
     */
    protected function createModel($path, $model)
    {
        $modelNameParse = explode('\\', $model);
        $model = end($modelNameParse);

        $content = File::get(__DIR__ . '/stubs/model.' . $this->option('type') . '.stub');
        $content = str_replace('ModelName', $model, $content);
        $content = str_replace('TableName', $this->option('table'), $content);

        File::put($path, $content);
    }

    /**
     * Создание миграции для модели.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = $this->option('table');

        if ($this->option('type') == 'base') {
            $this->call('make:migration', [
                'name' => "create_{$table}_table",
                '--create' => $table,
            ]);
        } else {
            $class = Str::studly($table);

            $content = File::get(__DIR__ . '/stubs/migration.tab.stub');
            $content = str_replace('TableClass', $class, $content);
            $content = str_replace('TableName', 'Create' . $table . 'Table', $content);

            $path = base_path() . '/database/migrations/' . date('Y_m_d_His') . '_create_' . $table . '_table.php';

            File::put($path, $content);
        }
    }
}
