<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_student' => $data['is_student'] ?? false,
        ]);

        $token = $user->createToken('web')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $data): ?array
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return null;
        }

        $isPlugin = isset($data['hardware_id']);

        if ($isPlugin) {
            $result = $this->handlePluginLogin($user, $data['hardware_id']);

            if ($result === 'device_mismatch') {
                return ['error' => 'device_mismatch'];
            }

            $token = $result;
        } else {
            $token = $user->createToken('web')->plainTextToken;
        }

        return [
            'user' => $user->load('activeSubscription.plan'),
            'token' => $token,
        ];
    }

    private function handlePluginLogin(User $user, string $hardwareId): string
    {
        if (! $user->hardware_id) {
            $user->update(['hardware_id' => $hardwareId]);
        } elseif ($user->hardware_id !== $hardwareId) {
            return 'device_mismatch';
        }

        $user->tokens()->where('name', 'plugin')->delete();

        return $user->createToken('plugin')->plainTextToken;
    }
}
