<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\User;
use App\Image;

class UnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    public function testRoute()
    {
        $response = $this->get('/');
        $response->assertStatus(302);
    }
    public function testLoginRoute()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }
    public function testLoginPost(){
        $response = $this->call('POST', '/login', [
            'email' => 'k.saipanas@gmail.com',
            'password' => 'chocolate..',
            '_token' => csrf_token()
        ]);
        $response->assertRedirect('/');
    }
    public function testLoginRouteAfterAuth()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }
    public function testRegisterRoute()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }
    public function testRegisterPost()
    {
        $name = $this->faker->name;
        $email = $this->faker->safeEmail;
        $password = $this->faker->password(8);

        $response = $this->post('register', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email
        ]);
    }
    public function testHomeRouteWithoutLogin()
    {
        $response = $this->get('/main');
        $response->assertRedirect('/login');
    }
    public function testHomeRoute()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('/main');
        $response->assertOK();
        $response->assertViewIs('pages.main');
    }
    public function testGalleryRouteWithoutLogin()
    {
        $response = $this->get('/gallery');
        $response->assertRedirect('/login');
    }
    public function testImageResouces()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('/lists');
        $response->assertOK();
    }
    public function testImageResoucesWithoutLogin()
    {
        $response = $this->get('/lists');
        $response->assertRedirect('/login');
    }
    public function testGroupRecources()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('/group');
        $response->assertStatus(200);
    }
    public function testGroupResoucesWithoutLogin()
    {
        $response = $this->get('/group');
        $response->assertRedirect('/login');
    }
    public function testStoreRoutePngNotOver10()
    {
        Storage::fake('upload');
        $file = UploadedFile::fake()->image('avatar.png')->size(1102); 
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/store', [
            'files' => $file,
        ]);
        $filename = $file->getClientOriginalName();
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $new_name = $file_name.'_'.time().'.' . $ext;
        $this->assertDatabaseHas('images', [
            'name' => $new_name,
        ]);
        $response->assertStatus(201);
        Storage::disk('upload')->assertExists($new_name);
    }
    public function testStoreRoutePngOver10(){
        Storage::fake('upload');
        $file = UploadedFile::fake()->image('avatar.png')->size(10245); 
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/store', [
            'files' => $file,
        ]);
        $filename = $file->getClientOriginalName();
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $new_name = $file_name.'_'.time().'.' . $ext;
        $this->assertDatabaseMissing('images', [
            'name' => $new_name,
        ]);
        $response->assertStatus(422);
        Storage::disk('upload')->assertMissing($new_name);
    }
    public function testStoreRouteJpgNotOver10(){
        Storage::fake('upload');
        $file = UploadedFile::fake()->image('avatar.jpg')->size(1102); 
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/store', [
            'files' => $file,
        ]);
        $filename = $file->getClientOriginalName();
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $new_name = $file_name.'_'.time().'.' . $ext;
        $this->assertDatabaseHas('images', [
            'name' => $new_name,
        ]);
        $response->assertStatus(201);
        Storage::disk('upload')->assertExists($new_name);
    }
    public function testStoreRouteJpgOver10(){
        Storage::fake('upload');
        $file = UploadedFile::fake()->image('avatar.jpg')->size(10245); 
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/store', [
            'files' => $file,
        ]);
        $filename = $file->getClientOriginalName();
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $new_name = $file_name.'_'.time().'.' . $ext;
        $this->assertDatabaseMissing('images', [
            'name' => $new_name,
        ]);
        $response->assertStatus(422); 
        Storage::disk('upload')->assertMissing($new_name);
    }
    public function testStoreRouteGifNotOver10(){
        Storage::fake('upload');
        $file = UploadedFile::fake()->image('avatar.gif')->size(1102); 
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/store', [
            'files' => $file,
        ]);
        $filename = $file->getClientOriginalName();
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $new_name = $file_name.'_'.time().'.' . $ext;
        $this->assertDatabaseHas('images', [
            'name' => $new_name,
        ]);
        $response->assertStatus(201);
        Storage::disk('upload')->assertExists($new_name);
    }
    public function testStoreRouteGifOver10(){
        Storage::fake('upload');
        $file = UploadedFile::fake()->image('avatar.gif')->size(10245); 
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/store', [
            'files' => $file,
        ]);
        $filename = $file->getClientOriginalName();
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $new_name = $file_name.'_'.time().'.' . $ext;
        $this->assertDatabaseMissing('images', [
            'name' => $new_name,
        ]);
        $response->assertStatus(422); 
        Storage::disk('upload')->assertMissing($new_name);
    }
    public function testDeleteGalleryApiWithDataExists()
    {
        $user = factory(User::class)->make();
        Storage::fake('upload');
        $image = factory(Image::class)->make();
        $file = UploadedFile::fake('upload')->image('avatar.png')->size(13);
        $response = $this->actingAs($user)->call('DELETE','api/image/1');
        $response->assertStatus(204);
    }
    public function testDeleteGalleryApiWithDataNotExists()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->call('DELETE','api/image/3');
        $response->assertStatus(404);
    }
   
}
