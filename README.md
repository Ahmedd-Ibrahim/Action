# Laravel Action Package

The Laravel Action package provides a convenient way to generate action classes in your Laravel application. Action classes encapsulate a specific unit of work and help to keep your code organized and maintainable.

## Installation

You can install the package via Composer by running the following command:

```bash
composer require packageName
```

## Usage

To generate a new action class, you can use the provided `make:action` Artisan command. Simply run the following command and provide the desired name for your action class:

```bash
php artisan action:make {name}
```

Replace `{name}` with the name of your action. The command will generate two files: an interface and a class representing your action.

The generated files will be placed in the configured directories. By default, the interface will be created in the `Actions\Contracts` directory, and the class will be created in the `Actions` directory. However, you can customize these paths in your configuration file.

## Configuration

The package provides a configuration file where you can customize various settings. To publish the configuration file, run the following command:

```bash
php artisan vendor:publish --tag=action-config
```
This will copy the configuration file to the `config` directory of your Laravel application. You can then modify the configuration values to suit your needs.

## Customizing Directories

If you want to change the default directories where the generated files are placed, you can update the configuration file. Open the `config/action.php` file and modify the `contracts_path` and `actions_path` values.

The `contracts_path` determines the directory for the generated interface files, and the `actions_path` determines the directory for the generated action class files. You can use forward slashes or backslashes to define the paths.

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, feel free to open an issue or submit a pull request.

