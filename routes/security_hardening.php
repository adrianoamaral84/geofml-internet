<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:hospede'])
    ->delete('/hospede/delete/meupedido/{id}', 'Security\SecureHospedeActionsController@deletePedido')
    ->name('hospede.delete.pedido');

Route::middleware(['auth', 'role:hospede'])
    ->get('/hospede/delete/meupedido/{id}', function () {
        abort(405, 'Método não permitido.');
    });

Route::middleware(['auth', 'role:hospede'])
    ->delete('/hospede/cancelar/hospedagem/{id}', 'Security\SecureHospedeActionsController@cancelarHospedagem')
    ->name('cancelar.hospedagem');

Route::middleware(['auth', 'role:hospede'])
    ->get('/hospede/cancelar/hospedagem/{id}', function () {
        abort(405, 'Método não permitido.');
    });
