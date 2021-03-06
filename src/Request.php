<?php

namespace StaticHTMLOutput;

class Request {

    /**
     * @var int
     */
    public $status_code;
    /**
     * @var mixed
     */
    public $default_options;
    /**
     * @var string
     */
    public $body;
    /**
     * @var string[]
     */
    public $headers;

    public function __construct() {
        $this->default_options = [
            CURLOPT_USERAGENT => 'StaticHTMLOutput.com',
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 600,
        ];
    }

    /**
     * @param resource $curl_handle
     */
    public function applyDefaultOptions( $curl_handle ) : void {
        foreach ( $this->default_options as $option => $value ) {
            curl_setopt(
                $curl_handle,
                $option,
                $value
            );
        }
    }

    /**
     * @param mixed[] $headers
     * @param mixed[] $data
     * @param mixed[] $curl_options
     */
    public function postWithJSONPayloadCustomHeaders(
        string $url,
        array $data,
        array $headers,
        array $curl_options = []
        ) : void {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );

        $this->applyDefaultOptions( $ch );

        if ( ! empty( $curl_options ) ) {
            foreach ( $curl_options as $option => $value ) {
                curl_setopt(
                    $ch,
                    (int) $option,
                    $value
                );
            }
        }

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode( $data )
        );

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $headers
        );

        $this->body = (string) curl_exec( $ch );
        $this->status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        curl_close( $ch );
    }

    /**
     * @param mixed[] $headers
     */
    public function getWithCustomHeaders( string $url, array $headers ) : void {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_HEADER, 1 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $headers
        );

        $this->applyDefaultOptions( $ch );

        $output = curl_exec( $ch );
        $this->status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        $header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );

        $this->body = substr( (string) $output, $header_size );
        $header = substr( (string) $output, 0, $header_size );

        $raw_headers = explode(
            "\n",
            trim( mb_substr( (string) $output, 0, $header_size ) )
        );

        unset( $raw_headers[0] );

        $this->headers = [];

        foreach ( $raw_headers as $line ) {
            list( $key, $val ) = explode( ':', $line, 2 );
            $this->headers[ strtolower( $key ) ] = trim( $val );
        }

        curl_close( $ch );
    }

    /**
     * @param mixed[] $headers
     * @param mixed[] $data
     */
    public function putWithJSONPayloadCustomHeaders(
        string $url,
        array $data,
        array $headers
        ) : void {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode( $data )
        );

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $headers
        );

        curl_setopt( $ch, CURLOPT_USERAGENT, 'StaticHTMLOutput.com' );

        $this->body = (string) curl_exec( $ch );
        $this->status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        curl_close( $ch );
    }

    /**
     * @param mixed[] $headers
     */
    public function putWithFileStreamAndHeaders(
        string $url,
        string $local_file,
        array $headers
        ) : void {
        $ch = curl_init();

        $file_stream = fopen( $local_file, 'r' );
        $data_length = filesize( $local_file );

        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_UPLOAD, 1 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_INFILE, $file_stream );
        curl_setopt( $ch, CURLOPT_INFILESIZE, $data_length );

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $headers
        );

        $this->applyDefaultOptions( $ch );

        $this->body = (string) curl_exec( $ch );
        $this->status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        curl_close( $ch );
    }

    /**
     * @param mixed[] $headers
     */
    public function postWithFileStreamAndHeaders(
        string $url,
        string $local_file,
        array $headers
        ) : void {
        $ch = curl_init();

        $file_stream = fopen( $local_file, 'r' );
        $data_length = filesize( $local_file );

        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_UPLOAD, 1 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_INFILE, $file_stream );
        curl_setopt( $ch, CURLOPT_INFILESIZE, $data_length );

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $headers
        );

        $this->applyDefaultOptions( $ch );

        $this->body = (string) curl_exec( $ch );
        $this->status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        curl_close( $ch );
    }

    /**
     * @param mixed[] $curl_options
     * @param mixed[] $data
     */
    public function postWithArray(
        string $url,
        array $data,
        array $curl_options = []
        ) : void {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );

        $this->applyDefaultOptions( $ch );

        if ( ! empty( $curl_options ) ) {
            foreach ( $curl_options as $option => $value ) {
                curl_setopt(
                    $ch,
                    (int) $option,
                    $value
                );
            }
        }

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $data
        );

        $this->body = (string) curl_exec( $ch );
        $this->status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        curl_close( $ch );
    }
}

