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
        $file = UploadedFile::fake()->image('avatar.png')->size(10485759); 
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->post('/store', [
            'files[]' => $file,
        ]);
        // $response->assertStatus(201);
        Storage::disk('upload')->assertExists(time().'0.'.$file->getClientOriginalExtension());
        Storage::disk('upload')->assertMissing('missing.jpg');
    }
    public function testStoreRoutePngOver10(){
        Storage::fake('upload');
        $file = UploadedFile::fake()->image('avatar.png')->size(10485761); 
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->post('/store', [
            'files[]' => $file,
        ]);
        // $response->assertStatus(201);
        Storage::disk('upload')->assertExists(time().'0.'.$file->getClientOriginalExtension());
        Storage::disk('upload')->assertMissing('missing.jpg');
    }
    /*public function testStoreRouteJpgNotOver10(){
        
    }
    public function testStoreRouteJpgOver10(){
        
    }
    public function testStoreRouteGifNotOver10(){
        
    }
    public function testStoreRouteGifOver10(){
        
    } */
    /* public function testDeleteGalleryApiWithDataExists()
    {
        $user = factory(User::class)->make();
        Storage::fake('upload');
        $image = factory(Image::class)->make();
        $file = UploadedFile::fake()->image('avatar.png')->size(10485759); 
        $response = $this->actingAs($user)->call('DELETE','images/7');
        dd($response);
        // $response->assertStatus(200);
        // $response->notSeeInDatabase('images', ['id' => 7]);
    } */
    /* public function testDeleteGalleryApiWithDataNotExists()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->call('DELETE','images/3');
        $response->assertStatus(404);
    } */
   
}
