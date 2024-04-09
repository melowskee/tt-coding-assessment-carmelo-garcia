<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PostRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Post;
use App\Models\Tag;
use Auth;
/**
 * Class PostCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PostCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Post::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/post');
        CRUD::setEntityNameStrings('post', 'posts');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }
    
    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        
        CRUD::setValidation([
            'title' => 'required|min:2',
            'description' => 'required|min:2',
            'tag_ids' => 'required',
        ]);
        //CRUD::setFromDb(); // set fields from db columns.
        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
        
        CRUD::field('user_id')->value(Auth::guard('backpack')->user()->id)->type('hidden');
        CRUD::field('title')->type('text');
        CRUD::field('description')->type('text');
        
        $options = Tag::all()->pluck('name', 'id')
        ->toArray();
        CRUD::field('tag_ids') 
        -> label('Tags')
        -> type('select_from_array')  // the type of Backpack field you want
        -> allows_null(false)
        -> allows_multiple(true)
        -> options($options);

    }
    public function store()
    {
        //dd($this->crud->getRequest()->route()->getActionMethod());
        $request = $this->crud->getRequest();

        $post = new Post();
        $post->user_id = $request->user_id;
        $post->title = $request->title;
        $post->description = $request->description;
        $post->save();

        $tags = $request->tag_ids;

        foreach ($tags as $tagId) {
            $tag = Tag::find($tagId);

            if ($tag) {
                // Attach the tag to the post
                $post->tags()->attach($tagId);
            }
        }
        
        //$response = $this->traitStore();
        return \Redirect::to($this->crud->route);
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
