<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('build.file.tree', function ($user) {
    return $user && $user->email === 'front@secopsforge.com';
});
