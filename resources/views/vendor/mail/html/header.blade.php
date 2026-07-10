<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="">
@else
<img src="{{asset('images/logo.png')}}" class="logo" alt="">
@endif
</a>
</td>
</tr>
