<?php

namespace Innoboxrr\RoutesToJson\Tests\Feature;

use Illuminate\Support\Facades\File;
use Innoboxrr\RoutesToJson\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * El comando corre contra el enrutador real de una aplicacion Testbench y
 * escribe en un directorio temporal propio de cada test, nunca en resources/.
 */
final class RouteToJsonCommandTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'routes-to-json-' . bin2hex(random_bytes(6));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);

        parent::tearDown();
    }

    protected function defineRoutes($router): void
    {
        $router->get('users/{id}/profile', fn () => 'ok')->name('user.profile');
        $router->get('invoices/{invoice}', fn () => 'ok')->name('invoice.show');
        $router->get('sin-nombre', fn () => 'ok');
        $router->post('otra-sin-nombre', fn () => 'ok');
    }

    #[Test]
    public function exporta_las_rutas_con_nombre_con_su_uri(): void
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . 'routes.json';
        config()->set('routes-to-json.path', $path);

        $this->artisan('route:json')->assertSuccessful();

        $routes = $this->readJson($path);

        $this->assertSame('users/{id}/profile', $routes['user.profile'] ?? null);
        $this->assertSame('invoices/{invoice}', $routes['invoice.show'] ?? null);
    }

    #[Test]
    public function omite_las_rutas_sin_nombre(): void
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . 'routes.json';
        config()->set('routes-to-json.path', $path);

        $this->artisan('route:json')->assertSuccessful();

        $routes = $this->readJson($path);

        $this->assertArrayNotHasKey('', $routes);
        $this->assertNotContains('sin-nombre', $routes);
        $this->assertNotContains('otra-sin-nombre', $routes);
        $this->assertArrayHasKey('user.profile', $routes);
    }

    #[Test]
    public function escribe_en_la_ruta_configurada(): void
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . 'otro-nombre.json';
        config()->set('routes-to-json.path', $path);

        $this->artisan('route:json')->assertSuccessful();

        $this->assertFileExists($path);
        $this->assertFileDoesNotExist(resource_path('vue/assets/json/routes.json'));
    }

    #[Test]
    public function crea_el_directorio_si_no_existe(): void
    {
        $nested = $this->directory . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'json';
        $path = $nested . DIRECTORY_SEPARATOR . 'routes.json';
        config()->set('routes-to-json.path', $path);

        $this->assertDirectoryDoesNotExist($nested);

        $this->artisan('route:json')->assertSuccessful();

        $this->assertDirectoryExists($nested);
        $this->assertArrayHasKey('user.profile', $this->readJson($path));
    }

    /**
     * @return array<string, string>
     */
    private function readJson(string $path): array
    {
        $this->assertFileExists($path);

        $decoded = json_decode((string) file_get_contents($path), true);

        $this->assertIsArray($decoded, 'El archivo generado no es JSON valido.');

        return $decoded;
    }
}
