# Caravel

**DISCLAIMER: This is a work in progress! Use at your own risk!**

A lightweight CRUD layer built on Laravel, for when a CMS is overkill.  Great for prototyping, but highly extendable.  It hooks into your Eloquent Models and automatically generates restful resource routes and views for basic CRUD management.  Bring your own authentication, view customizations, field types, etc.  [View a quick demo here.](http://recordit.co/hxPb7nh3RD)

- [Installation](#installation)
- [Field Configuration](#field-configuration)
- [Available Field Types](#available-field-types)
- [Add Field Types](#add-field-types)
- [Customize Views](#customize-views)
- [Authentication](#authentication)
- [Authorization](#authorization)

## Installation

### 1. Install into your Laravel.
```
composer require 'thisvessel/caravel:dev-master'
```
Note: I will tag a version when I am mostly happy with implementation.

### 2. Add CaravelServiceProvider to providers array in /config/app.php.
```php
ThisVessel\Caravel\CaravelServiceProvider::class,
```

### 3. Publish Caravel's config file.
```
php artisan vendor:publish --tag="caravel-config"
```

### 4. Add Eloquent Model mappings to resources array in /config/caravel.php.
```php
'resources' => [
    'products' => App\Product::class,
    'newsletters'  => App\Newsletter::class,
],
```

### 5. Copy Caravel's routes into your routes.php file.
```php
// Caravel Route Group
Route::group(['prefix' => config('caravel.prefix'), 'as' => 'caravel::'], function () {

    // Default Caravel Routes
    ThisVessel\Caravel\Routes::default();
});
```
To inspect which routes are dynamically generated, run the following command from your project root.
```
php artisan route:list
```
If you are configuring Caravel as a standalone installation, or you are routing through a subdomain, you might prefer setting a custom route `prefix` in `/config/caravel.php`.  Can be set `null`.

*That's it!  You now have a functional CRUD layer.*

## Field Configuration

Field configuration happens in your Eloquent Model.

```php
class Author extends Model
{
    /**
     * Caravel configuration.
     *
     * @var array
     */
    public $caravel = [
        'username' => 'required',
        'password' => 'type:password|required|min:8',
        'biography' => [
            'type'      => 'simplemde',
            'modifiers' => 'unlist',
            'rules'     => 'required|min:10',
            'label'     => 'Author Biography',
            'default'   => 'I am a default value.',
            'help'      => 'Help block text goes here.',
        ],
    ];
```

Your model's `$caravel` property allows you to define fillable fields, field modifiers, validation rules, etc. for the purpose of creating and updating in Caravel.  You are not required to set the `$fillable` property for the purpose of Caravel.  There are two ways to approach configuring a field:

1. Shorthand string configuration, which allows you to quickly specify field type (eg. `type:simplemde`), field modifiers (eg. `unlist`), as well as [Laravel Validation](https://laravel.com/docs/validation#available-validation-rules) rules.  Specify multiple modifiers and/or rules using pipe `|` separators.

2. Nested array configuration, which allows you to nest array elements for `type`, `modifiers`, `rules`, `label`, `default`, `help`, and `relation`.

## Available Field Types

The following field types are included with Caravel:

| Field Type      | Description                  |
| --------------- | ---------------------------- |
| input           | Basic text input             |
| textarea        | Basic textarea               |
| simplemde       | [Simplemde markdown editor](https://github.com/NextStepWebs/simplemde-markdown-editor) |
| password        | Basic password input         |
| select          | Basic select dropdown *      |
| select-multiple | Basic multiple select *      |
| radio           | Basic radio input group *    |
| checkbox        | Basic checkbox input group * |
| file            | Basic file input **          |

#### *Note on select, radio, and checkbox field types...
When using select, radio, or checkbox types, you may pass a reference to an [Eloquent Accessor](https://laravel.com/docs/eloquent-mutators#accessors-and-mutators) method for specifying available options. Example:
```php
'published' => 'type:radio,publishedOptions'
```
The second parameter in the above example is a reference to the publishedOptions accessor, which you can define on your model and use to dynamically generate radio options for your field:
```php
public function getPublishedOptionsAttribute()
{
    return [
        1 => 'Published',
        0 => 'Hidden',
    ];
}
```
This will generate a pair of radio options with binary boolean values:
```html
<label><input type="radio" name="published" value="1"> Published</label>
<label><input type="radio" name="published" value="0"> Hidden</label>
```

#### **Note on file input field type...
File upload handling not currently provided by Caravel. It is recommended that you leverage [Eloquent Mutators](https://laravel.com/docs/eloquent-mutators#accessors-and-mutators) to handle your file uploads.  Mutators open a wealth of possibilities.  For example, you could [handle your file upload](https://laravel.com/docs/requests#files) directly within that field's mutator, or you could pass the file object to an external service class to handle the upload. Examples and helpers to come soon.

## Add Field Types

You can add your new field types simply by referencing a new `type` string in your Model's `$caravel` field configuration.  This new type does not need to be registered anywhere.  Just be sure to provide Caravel with a proper view partial for this new field type.  A few notes on field type view partials:
- Place your new view partial within `/resources/views/vendor/caravel/fields`.
- You are responsible for displaying label, help block text, validation state, etc. correctly.
- A `$field` object is automatically passed into your view partial with necessary data for your markup (ie. name, label, required, help block text, etc.).
- A `$model` object is automatically passed into your view partial, in case you need access to other model properties.
- Finally, `$form` and `$bootForm` form builder objects are also passed into your view partial.  [Form](https://github.com/adamwathan/form) and [BootForms](https://github.com/adamwathan/bootforms) are excellent packages by [Adam Wathan](https://twitter.com/adamwathan).  Feel free to make use of these packages, otherwise plain old markup will work fine as well.

## Customize Views

You can easily override Caravel's views and view partials.  First publish Caravel's views to your project's /resources/views/vendor/caravel folder.
```
// Publish All Views
php artisan vendor:publish --tag="caravel-views"

// Publish Field View Partials Only
php artisan vendor:publish --tag="caravel-fields"
```
Once these views are published, you can modify anything within this folder.  Caravel will attempt to load your views before loading it's own default views.

## Authentication

Bring your own authentication!  Though Laravel ships with [Authentication](http://laravel.com/docs/authentication) features, you can easily apply any authentication middleware to Caravel's route group.  Don't forget to inform Caravel of your logout route so that the proper link can be displayed in the menu!  This can be specified in /config/caravel.php.

Need a login view?  You can publish Caravel's login view to /resources/views/auth using the following command.
```
php artisan vendor:publish --tag="caravel-auth"
```

## Authorization

If your authentication system is compatible with Laravel's [Authorization](http://laravel.com/docs/authorization) features, Caravel has preconfigured ability and policy checks for you to hook into.  For example, imagine you have the following resource mapping in `/config/caravel.php`:

```php
'resources' => [
    'posts' => App\Post::class,
],
```

Caravel uses this mapping to create the following ability and policy checks:

| Ability Definitions | Policy Methods | Behaviour                      |
| ------------------- | -------------- | ------------------------------ |
| manage-posts        | manage()       | Can user see posts in sidebar? |
| create-posts        | create()       | Can user create a post?        |
| update-post         | update()       | Can user update a post?        |
| delete-post         | delete()       | Can user delete a post?        |

If you define abilities or policy methods using the above naming conventions, Caravel will use your authorization logic where applicable.  Otherwise, Caravel skips the authorization check and gives the user full access.
