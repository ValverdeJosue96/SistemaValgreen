package com.valgreen.movil

data class Pedido(
    val id: Int,
    val total: Double,
    val anticipo: Double,
    val saldo: Double,
    val fecha_entrega: String?,
    val cliente: ClientePedido?,
    val estado_pedido: EstadoPedido?
)

data class ClientePedido(
    val nombres: String?,
    val primer_apellido: String?,
    val segundo_apellido: String?,
    val carnet: String?
)

data class EstadoPedido(
    val nombre: String?
)
