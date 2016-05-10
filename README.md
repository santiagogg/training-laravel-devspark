#[fit] training laravel devspark
*Esto repositorio contiene la presentación y el código utilizado en el training.*

---

#[fit]  Intro
*Dami*

Breve introducción a Laravel , MVC y composer.

---

#[fit] Practico
*Primera parte - Pato*

---

###[fit] 1. Instalación

Una vez instalado composer y el instalador de laravel podemos correr en consola el siguiente comando para crear un nuevo proyecto.

	laravel new training

---

Una vez instalado el proyecto corremos el siguiente comando para instalar las dependencias

	composer update
---
Ya podemos ver el sitio funcionando. Sino querés configurar los hosts podes ver el sitio en tu local host de esta manera.

	php artisan serve

Ya tenes una aplicación laravel corriendo en tu servidor.

---

###[fit] 2. Migrations / Seeds / Model Factories

Para poder probar nuestra aplicación vamos a generar algunos datos para el modelo Contact.

Para crear una migración

	php artisan make:migration create_contacts_table

para correr las migraciones por primera vez:

	php artisan migrate

Para crear un seeder

	php artisan make:seeder ContactsTableSeeder

Para correr los seeders

	php artisan db:seed 

Para crear un controlador

	php artisan make:controller --resource ContactController

---

#[fit] Interfaces y Repositorios

*practico segunda parte Santi*

---

##[fit]  Ejemplo de Controlador

	class ContactsController extends BaseController
	{
		public function all()
		{
			$contacts = \Contact::all();
			return View::make('contacts.index', compact('contacts'));
		}
	}

---

##[fit] No muy buena idea

1. Codigo muy acoplado
2. Dependiente de Eloquent
3. No es facil de testear
4. No es DRY (Don't repeat yourself)
5. Mantenimiento
6. Etc.

---

##[fit] Repositorios

* Actuan como una capa de servicio entre la aplicación y la base de datos.
* Por lo cual no interactuamos directamente con los modelos
* Se encapsula el comportamiento
* Se puede cambiar facilmente la capa de base de datos

---

##[fit] Ejemplo de Repositorio

	Class ContactRepository
	{
		public function all()
		{
			return \Contact::all();
		}
		public function find($id)
		{
			return \Contact::find($id);
		}
	}	

---

##[fit] Esta idea está un poco mejor
	
	class ContactsController extends BaseController
	{
		public function all()
		{
			$contactsRepo = new ContactRepository()
			$contacts = $contactsRepo->all();
			return View::make('contacts.index', compact('contacts'));
		}
	}

---

##[fit] Mmmm, Podría mejorarse...

* Fuertemente acoplado a un repo especifico
* Hay que evitar hacer new lo máximo posible

## Inyección de Dependencia

* Dependency Injection es un patrón de diseño que implementa inversion of control y permite  seguir el principio de inversion de dependencia.

* De esta manera pasamos la dependencia ( el servicio) al objeto.

---

##[fit] todavía no se entiende?

* #### En vez  de crear la clase dentro del objeto con new ContactRepository, vamos a inyectarlo al constructor.

* Mucho mas desacoplado
* Mucho más mantenible

---

##[fit] Interfaces
* Siempre que se pueda codificá pensando en contratos y no en la implementación.
* Representa Qué hace una clase y no el Cómo
* Las calses que implementan una interface deben proveer una implementación de sus metodos.
	* Codigo más desacoplado.

---

##[fit] Ejemplo de Interface

	interface ContactInterface
	{
		public function all();
		public function store($data);
	}
	
	class ContactRepository implements ContactInterface
	{
		public function all()
		{
			// some awesome code to retrieve all records
		}
		public function store($data)
		{
		// some awesome code to store a new contact
		}
	}

---

##[fit] Ejemplo de Inyección de dependencias

	class ContactsController extends BaseController
	{
		protected $contacts;
		public function __construct(ContactInterface $contacts)
		{
			$this->contacts = $contacts;
		}
	}

---

## Pero Cómo... ?

##[fit] Laravel is awesome...

	App::bind('ContactInterface','ContactRepository');

---

##[fit] Mejorando la idea...

	class ContactsController extends BaseController
	{
		protected $contacts;
		public function __construct(ContactInterface $contacts)
		{
			$this->contacts = $contacts;
		}
		public function all()
		{
			$contacts = $this->contacts->all();
			return View::make('contacts.index', compact('contacts'));
		}
	}

---

## Contact Interface

	interface ContactInterface
	{
		public function all();
		public function paginate($count);
		public function find($id);
		public function store($data);
		public function update($id, $data);
		public function delete($id);
		public function findBy($field, $value);
	}

---

## User Interface

	interface UserInterface
	{
		public function all();
		public function paginate($count);
		public function find($id);
		public function store($data);
		public function update($id, $data);
		public function delete($id);
		public function findBy($field, $value);
	}

---

## Job Interface
	
	interface JobInterface
	{
		public function all();
		public function paginate($count);
		public function find($id);
		public function store($data);
		public function update($id, $data);
		public function delete($id);
		public function findBy($field, $value);
	}

---

## Foo Interface

*Pará! , estamos repetiendo una y otra vez lo mismo...*

---

##[fit] Mejorando la idea...

	interface BaseInterface
	{
		public function all();
		public function paginate($count);
		public function find($id);
		public function store($data);
		public function update($id, $data);
		public function delete($id);
		public function findBy($field, $value);
	}

---

## Extend the BaseInterface
	
	interface ContactInterface extends BaseInterface {}
	interface UserInterface extends BaseInterface {}
	interface JobInterface extends BaseInterface {}
	Interface FooInterface extends BaseInterface {}


---

##[fit] La mejor idea

	class BaseRepository
	{
		protected $modelName;
		public function all()
		{
			$instance = $this->getNewInstance();
			return $instance->all();
		}
		public function find($id)
		{
			$instance = $this->getNewInstance();
			return $instance->find($id);
		}
		protected function getNewInstance()
		{
			$model = $this->modelName;
			return new $model;
		}
	}

---

##[fit] Mucho más limpio

	class ContactRepository extends BaseRepository
	{
		protected $modelName = 'Contact';
	}
	class UserRepository extends BaseRepository
	{
		protected $modelName = 'User';
	}

---

##[fit] Podemos hacer lo mismo para los controladores.

	class BaseAdminResoureceController extends AdminController
	{
		protected $repositoryInterface;
		protected $viewPath;
		public function __construct($interface)
		{
			$this->repositoryInterface = $interface;
		}
		public function index()
		{
			$items = $this->repositoryInterface->all();
			return View::make($this->viewPath . '.index',
			compact('items'));
		}
	}

	AdminOrdersController extends BaseAdminResoureceController
	{
		protected $viewPath = 'admin.orders';
		public function __construct(AdminOrderInterface $interface)
		{
			parent::__construct($interface);
		}
	}

---

##[fit] GRACIAS
	
***PREGUNTAS??***
