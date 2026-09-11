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
    ->delete('/hospede/cancelar/hospedagem/{id}', 'Security\SecureHospedeActionsController@cancelarReserva')
    ->name('cancelar.hospedagem');

Route::middleware(['auth', 'role:hospede'])
    ->get('/hospede/cancelar/hospedagem/{id}', function () {
        abort(405, 'Método não permitido.');
    });

Route::middleware(['auth', 'role:atendente|administrador_geral|auxiliar_administrador_geral'])
    ->delete('/atendente/hospedagem/{id}/checkin', 'Security\SecureHospedeActionsController@checkin')
    ->name('hospede.checkin');

Route::middleware(['auth', 'role:atendente|administrador_geral|auxiliar_administrador_geral'])
    ->delete('/atendente/hospedagem/{id}/checkout', 'Security\SecureHospedeActionsController@checkout')
    ->name('hospede.checkout');

Route::middleware(['auth'])
    ->get('/hospede/checkin/{id}', function () {
        abort(405, 'Método não permitido.');
    });

Route::middleware(['auth'])
    ->get('/hospede/checkout/{id}/{hospede}', function () {
        abort(405, 'Método não permitido.');
    });
