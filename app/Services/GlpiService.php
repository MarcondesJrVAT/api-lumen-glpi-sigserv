<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Log;

class GlpiService
{
    protected string $apiUrl;
    protected string $apiToken;
    protected string $appToken;
    protected string $sessionToken;

    public function __construct()
    {
        $this->apiUrl = env('GLPI_API_URL');
        $this->apiToken = env('GLPI_API_TOKEN');
        $this->appToken = env('GLPI_APP_TOKEN');
    }

    public function login()
    {
        try {
            $response = Http::withHeaders([
                'App-Token' => $this->appToken
            ])->post($this->apiUrl . '/initSession', [
                'login' => env('GLPI_USER'),
                'password' => env('GLPI_PASSWORD'),
                'api_token' => $this->apiToken
            ]);

            $result = $response->json();

            if (isset($result['session_token'])) {
                $this->sessionToken = $result['session_token'];
                return $this->sessionToken;
            }

            throw new \Exception('Erro ao autenticar no GLPI');
        } catch (\Exception $e) {
            Log::error('Erro ao autenticar no GLPI', [
                'erro' => $e->getMessage(),
            ]);
            throw $e;
        }

    }

    public function logout(): void
    {
        if ($this->sessionToken) {
            try {
                Http::withHeaders([
                    'App-Token' => $this->appToken,
                    'Session-Token' => $this->sessionToken,
                ])->post($this->apiUrl . '/killSession');
            } catch (\Exception $e) {
                Log::error('Erro ao encerrar sessão no GLPI', [
                    'erro' => $e->getMessage(),
                ]);
            } finally {
                $this->sessionToken = null;
            }
        }
    }

    protected function getSessionToken()
    {
        if (empty($this->sessionToken)) {
            $this->sessionToken = $this->login();
        }
        return $this->sessionToken;
    }

    protected function handleRequestWithReauth(callable $requestFunction): array
    {
        try {
            return $requestFunction();
        } catch (\Exception $e) {
            if ($e->getCode() === 401) {
                $this->login();
                return $requestFunction();
            }
            Log::error('Erro ao realizar requisição ao GLPI', [
                'erro' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getTickets(): array
    {
        return $this->handleRequestWithReauth(function () {
            $sessionToken = $this->getSessionToken();

            $response = Http::withHeaders([
                'App-Token' => $this->appToken,
                'Session-Token' => $sessionToken
            ])->get($this->apiUrl . '/Ticket', [
                'api_token' => $this->apiToken
            ]);

            return $response->json();
        });
    }

    public function getTicket(int $id): array
    {
        return $this->handleRequestWithReauth(function () use ($id) {
            $sessionToken = $this->getSessionToken();

            $response = Http::withHeaders([
                'App-Token' => $this->appToken,
                'Session-Token' => $sessionToken
            ])->get($this->apiUrl . '/Ticket/' . $id, [
                'api_token' => $this->apiToken
            ]);

            return $response->json();
        });
    }

    public function createTicket(array $data): array
    {
        return $this->handleRequestWithReauth(function () use ($data) {
            $sessionToken = $this->getSessionToken();

            $response = Http::withHeaders([
                'App-Token' => $this->appToken,
                'Session-Token' => $sessionToken
            ])->post($this->apiUrl . '/Ticket', [
                'api_token' => $this->apiToken,
                'input' => $data
            ]);

            return $response->json();
        });
    }

    public function updateTicket(int $id, array $data): array
    {
        return $this->handleRequestWithReauth(function () use ($id, $data) {
            $sessionToken = $this->getSessionToken();

            $response = Http::withHeaders([
                'App-Token' => $this->appToken,
                'Session-Token' => $sessionToken
            ])->put($this->apiUrl . '/Ticket/' . $id, [
                'api_token' => $this->apiToken,
                'input' => $data
            ]);

            return $response->json();
        });
    }

    public function deleteTicket(int $id): array
    {
        return $this->handleRequestWithReauth(function () use ($id) {
            $sessionToken = $this->getSessionToken();

            $response = Http::withHeaders([
                'App-Token' => $this->appToken,
                'Session-Token' => $sessionToken
            ])->delete($this->apiUrl . '/Ticket/' . $id, [
                'api_token' => $this->apiToken
            ]);

            return $response->json();
        });
    }
}
