# PHP Laravel

Completed: Yes
Tags: Article, Technology, videos
status: Done

## The Ultimate guide to install PHP Laravel in Ubuntu 20.04

### Step 1: Install Apache and PHP

```bash
sudo apt-get install apache2 php7.4 libapache2-mod-php7.4 php7.4-curl php-pear php7.4-gd php7.4-dev php7.4-zip php7.4-mbstring php7.4-mysql php7.4-xml curl -y

##start the Apache service and enable it to start
systemctl start apache2
systemctl enable apache2
```

### Step 2: Install Composer

```bash
sudo curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

##verify the installed version of the Composer with the following command:
composer --version

##You should get something like the following output:

Composer version 1.10.6 2020-05-06 10:28:10
```

### Step 3: Install Laravel Framework and create first project

```bash
cd /var/www/html
composer create-project laravel/laravel laravelapp --prefer-dist

cd laravelapp
sudo php artisan serve
```

---

## So,Let’s begin... What’s Laravel?

![Untitled](PHP%20Laravel%20a95169ee861b4b2db881adb584257b64/Untitled.png)

[https://www.notion.so](https://www.notion.so)

![Untitled](PHP%20Laravel%20a95169ee861b4b2db881adb584257b64/Untitled%201.png)

## After Installing Laravel and create the first project,How can we serve our app?

```bash
php artisan serve 
## the above line is the required one to start serving at port 8000 , but what if we need more customization
php artisan serve --port=8080
```

## MVC Design Pattern

```
--app
----Http
------Controllers
----Models
--resources
----views
```

## Add a new Library to Composer

- Search for package name in [https://packagist.org/](https://packagist.org/)
- Copy Installation Command into your Command Line
- run composer update

---

## General Process Template in Laravel

- Create your controller,model and migration
- Add A route in api/web.php

## Basic Routing

- **accept a URI and a closure**

```php
//Template
/*    Route::method('/${route}',function (){
					return view('${file_name}');
			});*/
// inside wep.php
//Route response --view

Route::get('/', function () {
    return view('welcome');
});

//or simply if your route returns a view you can use this shortcut
Route::view('/','welcome');

//Route response --string
Route::get('/users', function () {
    return 'Welcome to the users page';
});

//Route response --Array(JSON)
Route::get('/users', function () {
    return '["PHP","Laravel","React"]';
});

//Route response --JSON and attach Content-Type header
Route::get('/users', function () {
    return response()->json([
        'name' => 'PHP',
        'framework' => 'Laravel',
        'lang' => 'PHP',
    ]
    )->header('Content-Type', 'application/json');
});

```

- **Routes defined in the `routes/api.php`
 file are nested within a route group by the `RouteServiceProvider`
. Within this group, the `/api` URI prefix is automatically applied so you do not need to manually 
apply it to every route in the file.**
- **Sometimes you may need to register a route that responds to multiple HTTP verbs. You may do so using the `match` method. Or, you may even register a route that responds to all HTTP verbs using the `any` method:**

```php
Route::match(['get', 'post'], '/', function () {
    //
});

	Route::any('/', function () {
    //
});

```

## Route Params

```php
//inside web.php
// route param $name with constrain to be string
Route::get('/products/{name}', [ProductsController::class, 'show'])->where(
    'name', '[a-zA-Z]+'
);

//Some commonly used regular expression patterns have helper methods that allow you to quickly add pattern constraints to your routes:
Route::get('/user/{id}/{name}', function ($id, $name) {
    //
})->whereNumber('id')->whereAlpha('name');

//pass nested route param
Route::get('/products/{name}/{id}', [ProductsController::class, 'show'])->where([
    'name', '[a-zA-Z]+',
    'id', '[0-9]+',
]
);

//inside ProductsController.php
public function show($name)
    {
        $data = [
            'iphone' => 'iphone11',
            'samsung' => 'samsung s10',
        ];
        return view('products.index', [
            'product' => $data[$name] ?? 'Product ' . $name . " doesn't exist",
        ]);
				// ?? null check operator
    }

//optional params: Be sure to give it a default value in the call back function or int the corresponding function in controller
Route::get('/user/{name?}', function ($name = null) {
    return $name;
});
```

**If you would like a route parameter to always be constrained by a given regular expression, you may use the `pattern` method. You should define these patterns in the `boot` method of your `App\Providers\RouteServiceProvider` class:**

```php
/**
 * Define your route model bindings, pattern filters, etc.
 *
 * @return void
 */
public function boot()
{
    Route::pattern('id', '[0-9]+');
}
```

## Middleware

**To assign middleware to all routes within a group, you may use the `middleware` method before defining the group. Middleware are executed in the order they are listed in the array:**

```php
//Note group() used to group routes that share route attributes, such as middleware
Route::middleware(['first', 'second'])->group(function () {
    Route::get('/', function () {
        // Uses first & second middleware...
    });

    Route::get('/user/profile', function () {
        // Uses first & second middleware...
    });
});

//if a group of routes uses the same controller you may use the controller method like this:
Route::controller(ProductsController::class)->group(function () {
    Route::get('/orders/{id}', 'show');
    Route::post('/orders', 'store');
})
```

### Route Prefixes

**The `prefix` method may be used to prefix each route in 
the group with a given URI. For example, you may want to prefix all 
route URIs within the group with `admin`:**

```php
Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        // Matches The "/admin/users" URL
    });
});

```

### Implicit Binding

```php
use App\Models\User;
/***Since the $user variable is type-hinted as the App\Models\User Eloquent model and the variable name matches the {user}
 URI segment, Laravel will automatically inject the model instance that has an ID matching the corresponding value from the request URI. If a matching model instance is not found in the database, a 404 HTTP 
response will automatically be generated.*/**

Route::get('/users/{user}', function (User $user) {
    return $user->email;
});

//if you want Laravel to resolve Eloquent models using a column other than id you can do this:
//you may customize this behavior by calling the missing method when defining your route. The missing method accepts a closure that will be invoked if an implicitly bound model can not be found:
Route::get('/users/{user:email}', function (User $user) {
    return $user;
})
->missing(function (Request $request) {
            return Redirect::route('locations.index');
        });

```

```php
//you may define a route that will be executed when no other route matches the incoming request
//The fallback route should always be the last route registered by your application.
Route::fallback(function () {
    //
});

```

## Route Caching

**When deploying your application to production, you should take 
advantage of Laravel's route cache. Using the route cache will 
drastically decrease the amount of time it takes to register all of your
 application's routes. To generate a route cache, execute the `route:cache` Artisan command:**

```bash
php artisan route:cache
```

**After running this command, your cached routes file will be loaded on
 every request. Remember, if you add any new routes you will need to 
generate a fresh route cache. Because of this, you should only run the `route:cache` command during your project's deployment.**

**You may use the `route:clear` command to clear the route cache:**

```bash
php artisan route:clear
```

---

## Rendering a new view

- **inside views folder, create a new file with format ${file_name}.blade.php**
- **write your code inside it**
- **inside web.php file, setup a route for it like above -response=view**

---

# Controllers

**Controllers can group related request handling logic into a single class. For example, a `UserController` class might handle all incoming requests related to users, including showing, creating, updating, and deleting users.**

## Create a Controller using artisan

```bash
php artisan make:controller ProductsController
##Because of this common use case, Laravel resource routing assigns the typical create, read, update, and delete ("CRUD") 
##routes to a controller with a single line of code. To get started, we can use the make:controller Artisan command's --resource option 
##to quickly create a controller to handle these actions:
php artisan make:controller Photos --resources
## if you run php artisan route:list you will get something like this
+--------+-----------+---------------------+----------------+------------------------------------------------------------+------------------------------------------+
| Domain | Method    | URI                 | Name           | Action                                                     | Middleware                               |
+--------+-----------+---------------------+----------------+------------------------------------------------------------+------------------------------------------+
|        | GET|HEAD  | photos              | photos.index   | App\Http\Controllers\Photos@index                          | web                                      |
|        | POST      | photos              | photos.store   | App\Http\Controllers\Photos@store                          | web                                      |
|        | GET|HEAD  | photos/create       | photos.create  | App\Http\Controllers\Photos@create                         | web                                      |
|        | GET|HEAD  | photos/{photo}      | photos.show    | App\Http\Controllers\Photos@show                           | web                                      |
|        | PUT|PATCH | photos/{photo}      | photos.update  | App\Http\Controllers\Photos@update                         | web                                      |
|        | DELETE    | photos/{photo}      | photos.destroy | App\Http\Controllers\Photos@destroy                        | web                                      |
|        | GET|HEAD  | photos/{photo}/edit | photos.edit    | App\Http\Controllers\Photos@edit                           | web                                      |
+--------+-----------+---------------------+----------------+------------------------------------------------------------+------------------------------------------+
## To quickly generate an API resource controller that does not include the create or edit methods
php artisan make:controller PhotoController --api
```

The above command will create for us a controller file inside app/Http/Contollers like this:

![Untitled](PHP%20Laravel%20a95169ee861b4b2db881adb584257b64/Untitled%202.png)

Options available that we might use below with make:controller command

![Untitled](PHP%20Laravel%20a95169ee861b4b2db881adb584257b64/Untitled%203.png)

## Nested Resource

`Route::resource('photos.comments', PhotoCommentController::class);` this route will register a nested resource that might be accessed by URIs like this:

`**/photos/{photo}/comments/{comment}**`

```php
//You can customize parameter names for resoure routes like this
Route::resource('/cars.reviews', CarController::class)->parameters([
    "reviews" => 'review_id',
]);

//this will generate routes like this:
+--------+-----------+----------------------------------------+----------------------+------------------------------------------------------------+------------------------------------------+
| Domain | Method    | URI                                    | Name                 | Action                                                     | Middleware                               |
+--------+-----------+----------------------------------------+----------------------+------------------------------------------------------------+------------------------------------------+
|        | POST      | cars/{car_id}/reviews                  | cars.reviews.store   | App\Http\Controllers\CarController@store                   | web                                      |
|        | GET|HEAD  | cars/{car_id}/reviews                  | cars.reviews.index   | App\Http\Controllers\CarController@index                   | web                                      |
|        | GET|HEAD  | cars/{car_id}/reviews/create           | cars.reviews.create  | App\Http\Controllers\CarController@create                  | web                                      |
|        | DELETE    | cars/{car_id}/reviews/{review_id}      | cars.reviews.destroy | App\Http\Controllers\CarController@destroy                 | web                                      |
|        | PUT|PATCH | cars/{car_id}/reviews/{review_id}      | cars.reviews.update  | App\Http\Controllers\CarController@update                  | web                                      |
|        | GET|HEAD  | cars/{car_id}/reviews/{review_id}      | cars.reviews.show    | App\Http\Controllers\CarController@show                    | web                                      |
|        | GET|HEAD  | cars/{car_id}/reviews/{review_id}/edit | cars.reviews.edit    | App\Http\Controllers\CarController@edit                    | web                                      |
+--------+-----------+----------------------------------------+----------------------+------------------------------------------------------------+------------------------------------------+
```

## Single Action Controller

**A controller which `does one thing and one thing only.` Example: instead of making an EventsController to show past,present and upcoming events based on query parameter, make 3 controller one for each show function and implement your logic inside __invoke() function.**

```php
<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use App\Models\User;
 
class ShowPastEvents extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        // ...
    }
}

//inside web.php
Route::post('/events/past', ShowPastEvents::class);
```

---

## create a route using controller to return view

```php
//inside wep.php
// Route::get('/${route}',[class_name::class,${function_to_be_called_from_controller}]);
//if you use resources controller
//Route::resource('initial_endpoint',[class_name::class])

use App/Http/Controllers/ProductsController
Route::get('/products',[ProductsController::class,'index']);
Route::resource('/photos', Photos::class)

//When declaring a resource route, you may specify a subset of actions the controller should handle instead of the full set of default actions:
Route::resource('photos', PhotoController::class)->only([
    'index', 'show'
]);

//When declaring resource routes that will be consumed by APIs, you will commonly want to exclude routes that present HTML templates such as create and edit.
//For convenience, you may use the apiResource method
Route::apiResource('photos', PhotoController::class);

//If you need to add additional routes to a resource controller beyond the default set of resource routes,
// you should define those routes before your call to the Route::resource method

//inside ProductsController.php class

public function index()
{
	return view('products.index'); //the view index.blade.php lie inside view/products
}
```

## Passing Data to view

```php
public function index()
{
	$title="welcome to Laravel8 Course";
	$desc="Course Notes";
	//using compact
	return view('products.index',compact('title','desc'));

	//using with
	return view('products.index')->with('title', $title);

	//using array (not prefered)
	return view('products.index',[
		'title'=>$title,
		'desc'=>$desc
	]);
}

//inside index.blade.php
...
	<p> {{$title}}</p>
...
```

## Sharing Data With All Views

**You can edit `boot` function in `App\Providers\AppServiceProvider` adding `View::share(’key’,’value’);` to be shared among all views.**

## Optimizing Views

**Laravel provides the `view:cache` Artisan command to  precompile all of the views utilized by your application. For increased 
performance, you may wish to run this command as part of your deployment process.**

## Named Routes

```php
Route::get('/products',function(){
	return view('products.index');
})->name('products');

//you can use products as a route name in any place you want in the project like this
route('products') //output:  http://127.0.0.1:8000/products
```

---

## Blade Template

- Reusability using blade template
- can embed if statements , for loops , ... with html tags
    
    ### Using templates for fixed layouts and changed content using blade
    
    ```php
    <!--create folder and name it layouts!-->
    <!--inside layouts,create a file and name it app.blade.php!-->
    <!--app.blade will contain header and footer and between them @yield('content') like this!-->
    <!-- create files header.blade & footer.blade inside layouts folder !-->
    <!--  app.blade.php  !-->
    <html>
    	<header>
    		@include('layouts.header')
    	</header>
    	@yield('content')
    	<footer>
    		@include('layouts.footer')
    	</footer>
    </html>
    
    <!--  inside any file that use app.blade as a template for header and footer (say index.blade.php) !-->
    @extends('layouts.app')
    @section('content')
    	<!-- content in html  !-->
    @endsection
    ```
    
    ### Set active link in navigation bar (add class name based on specific route)
    
    ```php
    <a
    	 href="about"
    	 class={{request()->is('about')?'active':''}}
    	>
    About</a>
    ```
    
    ### include images
    
    ```php
    <!-- inside public folder,create images folder and put your images inside it  !-->
    <img src={{URL('images/icon-box.jpg')}} alt="">
    
    <!-- another way to do this and it is more secure create a symbolink folder storage and put your images inside it  !-->
    <img src={{URL('storage/icon-box.jpg')}} alt=""> 
    <!--  OR  !-->
    <img src={{asset('storage/icon-box.jpg')}} alt=""> 
    
    ```
    
    ## basics directives in blade
    
    ```php
    /*template for directives
    	@[dir]
    	enddir
    */
    //if-statement
    @if(5<10)
    	<p> 5 is less than 10</p>
    @elseif(5==10)
    	<p> 5 is indeed less than 10</p>
    @else
    	<p>all conditions are wrong!</p>
    @endif
    
    //switch-statement
    @switch($name)
        @case('John')
            <p> Name is John</p>
        @break
        @case('Dary')
            <p> Name is Dary</p>
        @break
        @case('Michael')
            <p> Name is Michael</p>
        @break
        @default
            <p> Name is not John, Dary or Michael</p>
    @endswitch
    
    //for-loop
    @for ($i = 0; $i < 10; $i++)
        <p>The value of i is {{ $i }}</p>
    @endfor
    
    //foreach-loop
    @foreach ($names as $name)
        <p>The name is {{ $name }}</p>
    @endforeach
    
    //forelse-loop
    @forelse ($names as $name )
        <p>The name is {{ $name }}</p>
    @empty
        <p>No names</p>
    @endforelse
    
    //while-loop
    {{ $i = 0 }}
    @while ($i < 10)
        <p>The value of i is {{ $i }}</p>
        {{ $i++ }}
    @endwhile
    ```
    

---

## Compiling assets (CSS,SASS,JS,... and much more)

### first you need to install node on your machine, if you’re not sure you have it or not you can check by writing *node -v* in the terminal and it should print out node version if installed,run *npm install* in your main laravel project direcotry, then open webpack.mix.js in your main laravel directory

```jsx
// webpack.mix.js
const mix = require('laravel-mix');
/*   template
	mix.[asset]('path of the file to be compiled','path of the compiled file')
*/
mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);
```

- **after any edits in webpack.mix.js file,if you want to see its effect run *npm run dev* or you can run *npm run watch* and it’ll compile the files after any change happened inside it**

---

### You can use any frontend framework you want via 2 easy commands

- **Go TO [https://laravel.com/docs/7.x/frontend](https://laravel.com/docs/7.x/frontend)**
- **Search for your framework you want to install ,(you can do it by a quick google search instead)**
- **copy commands,it’ll be something like that**

```bash
composer require laravel/ui
php artisan ui react
```

- **Then run *npm install* & *npm run dev***

---

## Database and migrations

**You must be sure that you have mysql or any other database driver installed on your machine before beginning,here we will use mysql.**

### create model in laravel with its migration

```bash
## template
## php artisan make:modal ${modal name in singular} -m (to create its migration)
php artisan make:modal post -m
```

**after create modal and build the table in its migration file something like that**

![Untitled](PHP%20Laravel%20a95169ee861b4b2db881adb584257b64/Untitled%204.png)

then run:

```bash
php artisan migrate
## for further informations about migration command and options go to https://laravel.com/docs/8.x/migrations

##To accumulate all migrations files into single schema sql file
php artisan schema:dump --prune

##To see which migrations have run so far
php artisan migrate:status

##Roll back + migrate
php artisan migrate:refresh
 
##Refresh the database and run all database seeds...
php artisan migrate:refresh --seed
```

**To roll back the latest migration operation, you may use the `rollback` Artisan command. This command rolls back the last "batch" of migrations, which may include multiple migration files:**

```bash
**php artisan migrate:rollback**
```

**You may roll back a limited number of migrations by providing the `step` option to the `rollback` command. For example, the following command will roll back the last five migrations:**

```bash
**php artisan migrate:rollback --step=5**
```

**The `migrate:reset` command will roll back all of your application's migrations:**

```bash
**php artisan migrate:reset**
```

## Factory Model

**You can fill your database tables with dummy data using factoris**

```bash
php artisan make:factory postFactory --model=Post
```

**Then return tables’columns with its data type like this in postFactory file**

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use Illuminate\Support\Str;

class postFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->title(),
            'body' => $this->faker->paragraph(),
            'created_at' => now(),
        ];
    }
}
```

**Then run *php artisan tinker*, inside it run *\App\Models\Post::factory()→count(20)→create();* and it will create 20 rows with dummy data.**

## Best way to generate records (Database Seeders)

`php artisan make:seed ${SeederName}` 

```php
class postsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::factory()->count(10)->create();
    }
}
```

**Then in DatabaseSeeder file add all of your seeders**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            postsSeeder::class,
        ]);
    }
}
```

**Then run `php artsian db:seed`**

---

## Query Builder

**You can either write sql queries as normal or use provided methods in laravel.**

```php
//inside postController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PostsController extends Controller
{
    public function index()
    {
        $id = 1;
        $posts = DB::select('SELECT * FROM posts WHERE id= :id', ['id' => $id]);
        // $returned = DB::table('posts')->where('title', 'Mr.')->get();
        // $returned = DB::table('posts')->select('title', 'body')->where('title', 'Mr.')->get();
        // $returned = DB::table('posts')->where('title', 'Mr.')->orderBy('id', 'desc')->get();
				//The Laravel query builder uses PDO parameter binding to protect your application against SQL injection attacks.
			  //There is no need to clean or sanitize strings passed to the query builder as query bindings.
        $returned = DB::table('posts')->where('title', 'Mr.')->orderBy('id', 'desc')->count();
				//If you already have a query and want to add column to it
				$users = $query->addSelect('age')->get();
        // dd($posts);
        dd($returned);
    }
}
```

### select specific columns

`$titles = DB::table('users')->pluck('title', 'name');`

### Chunking Results

```php
use Illuminate\Support\Facades\DB;

DB::table('users')->orderBy('id')->chunk(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});

//If You want to update the DB Records while Chunking 
DB::table('users')->where('active', false)
    ->chunkById(100, function ($users) {
        foreach ($users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['active' => true]);
        }
    });

```

### Join Method

`DB::table(’table1’)→join(’table2’,’table1.col1’,’operator’,’table2.col2’)`

### Grouping Where Clause

**If you need to group an "or" condition within parentheses, you may pass a closure as the first argument to the `orWhere` method:**

```php
$users = DB::table('users')
            ->where('votes', '>', 100)
            ->orWhere(function($query) {
                $query->where('name', 'Abigail')
                      ->where('votes', '>', 50);
            })
            ->get();

```

**The example above will produce the following SQL:**

```sql
select * from users where votes > 100 or (name = 'Abigail' and votes > 50)

```

## Create new instance into table using Elequent Model

**first,disable csrf verification in developement stage by editing \App\Http\Middleware\VerifyCsrfToken.php**

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
        'http://localhost:8000/*', // type here your IPAddress
    ];
}
```

**Now,You can edit store function in your controllor file**

```php
public function store(Request $request)
    {
				// to create a new Rule for validation run thims command in terminal
				// php artisan make:rule ${RuleName}
				// then write the logic of validation inside function passes and the message in case of failure in function message 
		  $request->validate([
            'name' => "required|unique:cars",
            'founded' => 'required|integer',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5048',
        ]);

        $newImageName = time() . $request->name . '.' . $request->image->getClientOriginalExtension();
        $request->image->move(public_path('images'), $newImageName);

        $car = Car::create([
            'name' => $request->input('name'),
            'founded' => $request->input('founded'),
            'description' => $request->input('description'),
            'image_path' => $newImageName,
        ]);
    }
```

# Database Transactions

```php

use Illuminate\Support\Facades\DB;
 
DB::transaction(function () {
    DB::update('update users set votes = 1');
 
    DB::delete('delete from posts');
	});
//The transaction method accepts an optional second argument which defines the number of times a transaction should be retried when a deadlock occurs.
//Once these attempts have been exhausted, an exception will be thrown:

use Illuminate\Support\Facades\DB;
 
DB::transaction(function () {
    DB::update('update users set votes = 1');
 
    DB::delete('delete from posts');
}, 5);
```

**If you would like to connect to your database's CLI, you may use the `db` Artisan command:**

```bash
**php artisan db**
```

## Validation

**instead of repeating yourself by copying the above lines of validation any where you want to validate on these inputs,you can make formRequest validation via *php artisan make:request ${RequestName}*
Then put the validation inside function rules in RequestName.php file like this**

- **Optional data should be marked as nullable.**

```php
//Be sure to set Accept header to application/json to receive json response on failure.
public function rules()
    {
        return [
            'name' => "required|unique:cars",
            'founded' => 'required|integer',
            'description' => 'required',
        ];
    }

//then you can use it to validate like this in carController.store or any function use it
public function store(${RequestName} $request)
    {

		    $request->validated();

        $car = Car::create([
            'name' => $request->input('name'),
            'founded' => $request->input('founded'),
            'description' => $request->input('description'),
        ]);
        return response()->json($car, 201);
    }
```

## Password Validation

```php
Password::min(8)
    ->letters()
    ->mixedCase()
    ->numbers()
    ->symbols()
    ->uncompromised()
```

**You may find it convenient to specify the default validation rules for passwords in a single location of your application. You can easily 
accomplish this using the `Password::defaults` method, which accepts a closure. The closure given to the `defaults` method should return the default configuration of the Password rule. Typically, the `defaults` rule should be called within the `boot` method of one of your application's service providers:**

```php
use Illuminate\Validation\Rules\Password;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Password::defaults(function () {
        $rule = Password::min(8);

        return $this->app->isProduction()
                    ? $rule->mixedCase()->uncompromised()
                    : $rule;
    });
}

```

**Then, when you would like to apply the default rules to a particular password undergoing validation, you may invoke the `defaults` method with no arguments:**

```php
'password' => ['required', Password::defaults()],

```

## Eloquent Serialization

```php
public function show($id)
    {
        //you can convert collection returned from here to array using toArray method and you can access its attributes like this car['attr']
        $car = Car::find($id)->toArray();

        //you can convert collection returned from here to json using toJson method and you can access its attributes like this car->attr
        $car = Car::find($id)->toJson();
				// in order to make json iteratable use json_decode
				 $car = json_decode($car); 
        return $car ? $car : response()->json(['error' => 'Car not found'], 404);
    }
```

---

## Eloquent one to many

```php
// refernced Model
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'cars';
    protected $fillable = ['name', 'founded', 'description'];

    public function carModels() // prefered to have the same name as refrenced model
    {
        return $this->hasMany(CarModel::class);
    }
}

	//referencing model
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'car_models';
    public function car() // prefered to have the same name as refrencing model
    {
        return $this->belongsTo(Car::class);
    }
}
```

---

## Rate Limiter

**Open `App\Providers\RouteServiceProvider` ,in Function `configureRateLimiting` You can set your rate limit config inside it:** 

```php
protected function configureRateLimiting()
{
		/*
	    RateLimiter::for('${limiter_name}',closure);
		*/
    RateLimiter::for('global', function (Request $request) {
        return Limit::perMinute(1000);
    });

		//You can customize response if limit is exceeded
    RateLimiter::for('global', function (Request $request) {
        return Limit::perMinute(1000)->response(function(){
					return response('limit exceeded',429)
				})
		});
		
		//You can segent your rate limit using by method
		// Here you limit 100 requsest per minute per IP Address
		RateLimiter::for('uploads', function (Request $request) {
        return Limit::perMinute(1000)->by($request->ip());
    });
}

//Attaching rate limit to a specific route using throttle middleware
Route::middleware(['throttle:uploads'])->group(function () {
		Route::post('/audio', function () {
        //
    });
});
```

---

## Middleware

**Middleware provide a convenient mechanism for inspecting and filtering HTTP requests entering your application.**

## Defining a middleware

**You can easily create your own middleware using artisan command `php artisan make:middelware EnsureToken` then you can find your middleware class inside `app/Http/Middleware` directory.**

```php
<?php
 
namespace App\Http\Middleware;
 
use Closure;
 
class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
				// if the given token doesn't match 'my-secret-token' it'll redirect to homepage 
        if ($request->input('token') !== 'my-secret-token') {
            return redirect('home');
        }
				// if matched, proceed to next middleware or start handling request if none.
        return $next($request);
    }
}
```

If you want a middleware to run during every HTTP request to your application, list the middleware class in the `$middleware`property of your `app/Http/Kernel.php`class.But if you want to assign middleware to specific route you have two methods below:

```php
Route::post('/posts/{post}', [PostsController::class, 'index'])->middleware('\App\Http\Middleware\EnsureToken');
//OR Route::post('/posts/{post}', [PostsController::class, 'index'])->middleware(EnsureToken::class);
//OR you should first assign the middleware a key in your application's app/Http/Kernel.php
//Kernel.php:
protected $routeMiddleware = [
    ...
		'ensureToken' => 'App\Http\Middleware\EnsureToken',
];

//inside web.php:
Route::post('/posts/{post}', [PostsController::class, 'index'])->middleware('ensureToken');
// You can send params to middleware and accept it in handle func as a param after $next closure 
Route::post('/posts/{post}', [PostsController::class, 'index'])->middleware('ensureToken:admin');

```

## Assigning middleware to controller using __construct() method

```php
class UserController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('log')->only('index');
        $this->middleware('subscribed')->except('store');
    }
}
```

---

## HTTP Requests

**Used to retrieve the input, cookies, and files that were submitted with the request.**

### Important methods on Request Class

- `$request→path();` returns `/foo/bar` if the url is `http://example.com/foo/bar`
- `$request→is(String)` checks if the route path matches a specific pattern
- `$request→routeIs(String)` check if the route path has a specific route name
- `$request→method()` will return the HTTP verb for the request.
- `$request→isMethod(HTTP Verb)` verify if matched a given string
- `$request→header(headerName,defaultValueToBeReturnedIfNotFound)` retrieve request header
- `$request→hasHeader(headerName)` check if request has a specific header
- `$request→bearerToken()` retrieve bearer token from Authorization header
- `$request→mergeIfMissing(['votes'=>0])` merge additional input to the request’s existing input data
- `$request→file(attributeName)` retrieve uploaded files.
- `$request→file(attributeName)→isValid()` To verify that file is uploaded with no problems.
- `$request→file(attributeName)->storeAs(dirName,fileNameWithExt);` used to store the file uploaded.

### Flashing Input Then Redirecting

**Since you often will want to flash input to the session and then redirect to the previous page, you may easily chain input flashing onto a redirect using the `withInput` method:**

```php
return redirect('form')->withInput();

return redirect()->route('user.create')->withInput();

return redirect('form')->withInput(
    $request->except('password')
);

//You can retrieve old input using:
$username = $request->old('username');
```

**Laravel also provides a global `old` helper. If you are displaying old input within a Blade template, it is more convenient to use the `old` helper to repopulate the form. If no old input exists for the given field, `null` will be returned:**

```html
<input type="text" name="username" value="{{ old('username') }}">
```

---

## Responses

```php
//most response headers are chainable,you can add a series of headers to the response before sending it back to the user:
return response($content)
            ->header('Content-Type', $type)
            ->header('X-Header-One', 'Header Value')
            ->header('X-Header-Two', 'Header Value');
//OR You can use withHeaders method
return response($content)
            ->withHeaders([
                'Content-Type' => $type,
                'X-Header-One' => 'Header Value',
                'X-Header-Two' => 'Header Value',
            ]);
//You may also generate redirects to controller actions. To do so, pass the controller and action name to the action method:
return redirect()->action([UserController::class, 'index']);
return redirect()->away('https://www.google.com') //is used to redirect to external domain
//The download method may be used to generate a response that forces the user's browser to download the file at the given path.
//Only pathToFile is the mandatory param
return response()->download($pathToFile, $name, $headers);

//The file method may be used to display a file, such as an image or PDF, directly in the user's browser instead of initiating a download.
return response()->file($pathToFile, $headers);

```

---

# Sessions

**Your application's session configuration file is stored at `config/session.php`.**

**When using the `database` session driver, you will need to create a table to contain the session records. You may use the `session:table` Artisan command to generate this migration.**

## Retrieving Session Data

You can retrieve session data via Request instance or session global helper function like this:

```php
$value = $request->session()->get('key');
$value = $request->session()->get('key','defaultValueIfNull');
$value = $request->session()->get('key',closure);
$value = $request->session()->pull('key', 'default');
$request->session()->put('key', 'valueToBeStoredInKey');

$value = session('key');
$value = session('key','defaultValueIfNull');
session(['key'=>'valueToBeStoredInKey']);

if($request->session()->has('key')){ //return rtrue if the item is present and isn't null
	//
}

if($request->session()->exists('key')){ //return rtrue if the item is present even if it's null
	//
}

```

---

# Error Handling

- **During local development, you should set the `APP_DEBUG`environment variable to `true`.In your production environment, this value should always be `false`.If the value is set to `true` in production, you risk exposing sensitive configuration values to your application's end users.**
- **If You want to customize error code page,all you should do is creating errors/{errorCode}.blade.php inside your views directory and it will render instead of default page.**
- Exception VS \Exception
    
    ```php
    <?php
    namespace Module\Example;
    
    class Test
    {
        try{
    
        } catch(Exception $e) { // will look up Module\Example\Exception
    
        }
    
        try{
    
        } catch(\Exception $e) { // will look up Exception from global space
    
        }
    }
    ```
    
- **If you want to ignore specific exception type ad never report you can add it to `$dontReport` in `app/Exceptions/Handler`.**
- **You can create your own exception using `php artisan make:exception {exceptionName}` and implement your own `report` and `render` methods.**
- **If your exception extends an exception that is already renderable, such as a built-in Laravel or Symfony exception, you may return `false` from the exception's `render` method to render the exception's default HTTP response.**

---

# Authentication

## Registering A New User

- **Create a simple Register Controller which accepts name, email and password from the user and store it in the User Model.**
- **Don’t forget to add `StartSession` and `ShareErrorsFromSession` middleware if not added to route group.**

```php
class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'registerd successfully!',
        ], 200);

    }
}
```

## Login

- **Then you can simply create LoginController**

```php
class LoginController extends Controller
{
		public function authenticate(Request $request)
    {
        $credentials = $this->extractCredentials($request);
					
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return $this->sendResponse(['message' => 'Login successful',
                'user' => Auth::user(),
            ], 200);
        }

        return $this->sendResponse(['message' => 'Login failed', 'credentials' => $credentials], 401);
    }
    
    private function extractCredentials(Request $request)
    {
        return $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    }

    private function sendResponse($jsonMsg, $statusCode)
    {
        return response()->json($jsonMsg, $statusCode);
    }
}
```

## Logout

```php
class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out'], 200);
    }
}
```

## Logout other devices

**Before getting started, you should make sure that the `Illuminate\Session\Middleware\AuthenticateSession` middleware is present and un-commented in your `App\Http\Kernel`class' `web` middleware group or the middle group you u**se.

**Then, you may use the `logoutOtherDevices`method provided by the `Auth`facade.**

```php
Auth::logoutOtherDevices($currentPassword);
```

## Password Confirmation

- **you may configure the length of time before the user is re-prompted for their password by changing the value of the `password_timeout` configuration value within your application's `config/auth.php`configuration file.**

```php
public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            return $this->sendResponse(['message' => 'Password is invalid'], 200);
        }
        $request->session()->passwordConfirmed();
        return $this->sendResponse(['message' => 'Password Confirmed'], 401);
    }

//protecting routes
Route::post('/settings', function () {
    // ...
})->middleware(['password.confirm']);
```

- **In case you try to access protected route needed to confirm password you’ll get response**
    
    ![Untitled](PHP%20Laravel%20a95169ee861b4b2db881adb584257b64/Untitled%205.png)
    

---

## Authorization using Sanctum.

[Laravel - The PHP Framework For Web Artisans](https://laravel.com/docs/8.x/sanctum)

---

# Email Verification

- **Configure your .env file if you use mailtrap like this:**

```php
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=2*******38f0
MAIL_PASSWORD=0*********cf
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=kahmd1444@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

- **Be Sure to install php-mbstring and php-xml**
    - if not installed, `sudo apt install php-mbstring php-xml`
- **confiqure your route file like this**

```php
Route::get('/email/verify', function () {
    return view('index');
})->middleware('auth')->name('verification.notice'); //important step to name the route

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth:sanctum'])->name('verification.verify');
```

- **After Clicking on Verify Email Address in the mail sent you should found the record in the users table in the database has been updated like the last one here:**

![Untitled](PHP%20Laravel%20a95169ee861b4b2db881adb584257b64/Untitled%206.png)

---

## Stripe Payment

[https://stripe.com/docs/api/tokens/create_card?lang=php](https://stripe.com/docs/api/tokens/create_card?lang=php) 

[https://www.itsolutionstuff.com/post/laravel-9-stripe-payment-gateway-integration-tutorialexample.html](https://www.itsolutionstuff.com/post/laravel-9-stripe-payment-gateway-integration-tutorialexample.html) 

---

## Polymorphic relationships

### One To One

the child model can belong to more than one type of model using a single association. For example, a blog `Post` and a `User` may share a polymorphic relation to an `Image` model.

```
posts
    id - integer
    name - string

users
    id - integer
    name - string

images
    id - integer
    url - string
    imageable_id - integer
    imageable_type - string
```

Note the `imageable_id` and `imageable_type` columns on the `images` table. The `imageable_id` column will contain the ID value of the post or user, while the `imageable_type` column will contain the class name of the parent model.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * Get the parent imageable model (user or post).
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}

class Post extends Model
{
    /**
     * Get the post's image.
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

class User extends Model
{
    /**
     * Get the user's image.
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

```

---

## Mutators

[New in Laravel 8.77: One Method for Accessors and Mutators in Eloquent](https://youtu.be/P3J4wVSlKnQ)

---

### [Soft Deleting](https://laravel.com/docs/9.x/eloquent#soft-deleting)

In addition to actually removing records from your database, Eloquent can also "soft delete" models. When models are soft deleted, they are not actually removed from your database. Instead, a `deleted_at` attribute is set on the model indicating the date and time at which the model was "deleted". To enable soft deletes for a model, add the `Illuminate\Database\Eloquent\SoftDeletes` trait to the model:

```php
<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Flight extends Model
{
    use SoftDeletes;
}
```

You should also add the `deleted_at` column to your database table. The Laravel [schema builder](https://laravel.com/docs/9.x/migrations) contains a helper method to create this column:

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
Schema::table('flights', function (Blueprint $table) {
    $table->softDeletes();
});
 
Schema::table('flights', function (Blueprint $table) {
    $table->dropSoftDeletes();
});
```

---

## File Uploads

[Easy File Uploading With JavaScript | FilePond](https://pqina.nl/filepond/docs/)

[Laravel File Upload with FilePond: Step-by-Step](https://youtu.be/GRXaCfS1qj0)