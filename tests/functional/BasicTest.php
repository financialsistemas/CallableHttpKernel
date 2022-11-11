<?php

namespace functional;

use PHPUnit\Framework\TestCase;
use Stack\CallableHttpKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicTest extends TestCase
{
    /** @dataProvider provideRequests */
    public function testHelloWorld(Request $request)
    {
        $kernel = new CallableHttpKernel(function (Request $request) {
            return new Response('Hello World!');
        });

        $response = $kernel->handle($request);
        $this->assertEquals(new Response('Hello World!'), $response);
    }

    public function testMultipleCalls()
    {
        $kernel = new CallableHttpKernel(function (Request $request) {
            static $i = 0;
            return new Response($i++);
        });

        $response = $kernel->handle(Request::create('/'));
        $this->assertEquals(new Response('0'), $response);

        $response = $kernel->handle(Request::create('/'));
        $this->assertEquals(new Response('1'), $response);

        $response = $kernel->handle(Request::create('/'));
        $this->assertEquals(new Response('2'), $response);
    }

    public function nonResponseReturnValueShouldThrowException()
    {
        $kernel = new CallableHttpKernel(function (Request $request) {
            return 'foo';
        });

        $this->expectException(\UnexpectedValueException::class);

        $kernel->handle(Request::create('/'));
    }

    public function provideRequests()
    {
        return array(
            array(Request::create('/')),
            array(Request::create('/foo')),
            array(Request::create('/foo/bar/baz?qux=quux')),
            array(Request::create('/', 'POST')),
            array(Request::create('/', 'PUT')),
            array(Request::create('/', 'DELETE')),
            array(Request::create('/foo?wat=wob', 'POST')),
        );
    }
}
