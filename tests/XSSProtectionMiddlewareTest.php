<?php

use GlobalInitiative\XSSProtection\Middleware\XSSProtection;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;


class XSSProtectionMiddlewareTest extends TestCase
{
    /** @test */
    public function strips_out_script_tags()
    {
        $request = new Request;
        $request->setMethod('POST');

        $request->merge([
            'string' => 'A test string with <script>script tags</script> inside it',
        ]);

        $middleware = new XSSProtection();

        $middleware->handle($request, function ($req) {
            $this->assertEquals('A test string with  inside it', $req->string);
        });
    }

    /** @test */
    public function strips_out_iframe_tags()
    {
        $request = new Request;
        $request->setMethod('POST');

        $request->merge([
            'string' => 'A test string with <iframe>iframe tags</iframe> inside it',
        ]);

        $middleware = new XSSProtection();

        $middleware->handle($request, function ($req) {
            $this->assertEquals('A test string with  inside it', $req->string);
        });
    }

    /** @test */
    public function does_not_strip_out_other_tags()
    {
        $request = new Request;
        $request->setMethod('POST');

        $request->merge([
            'string' => 'A test string with <p>other</p> <strong>tags</strong> inside it',
        ]);

        $middleware = new XSSProtection();

        $middleware->handle($request, function ($req) {
            $this->assertEquals('A test string with <p>other</p> <strong>tags</strong> inside it', $req->string);
        });
    }
}
