
package com.valgreen.movil

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class ProductoAdapter(
    private val productos: List<Producto>
) : RecyclerView.Adapter<ProductoAdapter.ProductoViewHolder>() {

    class ProductoViewHolder(view: View) :
        RecyclerView.ViewHolder(view) {

        val nombre: TextView =
            view.findViewById(R.id.txtNombreProducto)

        val categoria: TextView =
            view.findViewById(R.id.txtCategoriaProducto)

        val descripcion: TextView =
            view.findViewById(R.id.txtDescripcionProducto)

        val precio: TextView =
            view.findViewById(R.id.txtPrecioProducto)
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ProductoViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_producto, parent, false)

        return ProductoViewHolder(view)
    }

    override fun onBindViewHolder(
        holder: ProductoViewHolder,
        position: Int
    ) {
        val producto = productos[position]

        holder.nombre.text = producto.nombre
        holder.categoria.text =
            producto.categoria ?: "Sin categoría"
        holder.descripcion.text =
            producto.descripcion ?: "Sin descripción"
        holder.precio.text = "Bs ${producto.precio}"
    }

    override fun getItemCount(): Int = productos.size
}
