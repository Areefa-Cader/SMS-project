<x-mail::message>
# Change Password Request

Click on the button below to change the password.

<x-mail::button :url="'http://localhost:4200/reset-password?token='.$token">
Reset Password
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
