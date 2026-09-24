<x-mail::message>
# Welcome to the Alaminos-Infanta ROTC Portal

Hi {{ $user->cadet->first_name }},

Your ROTC enrollment has been **approved**. You may now log in using the temporary credentials below.

**Username:** {{ $user->username }}
**Temporary Password:** {{ $temporaryPassword }}

<x-mail::button :url="config('app.url') . '/login'">
Log in to your account
</x-mail::button>

For security, you will be required to set a new password the first time you log in.

If you did not request this account, please contact the ROTC office immediately.

Thanks,<br>
Alaminos-Infanta ROTC Portal
</x-mail::message>