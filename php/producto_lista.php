<?php




	$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
	$tabla="";

	$campos="producto.producto_id,producto.producto_codigo,producto.producto_nombre,producto.producto_precio,producto.producto_stock,producto.producto_foto,producto.categoria_id,producto.usuario_id,categoria.categoria_id,categoria.categoria_nombre,usuario.usuario_id,usuario.usuario_nombre,usuario.usuario_apellido";

	if(isset($busqueda) && $busqueda!=""){

		$consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id INNER JOIN usuario ON producto.usuario_id=usuario.usuario_id WHERE producto.producto_codigo LIKE '%$busqueda%' OR producto.producto_nombre LIKE '%$busqueda%' ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(producto_id) FROM producto WHERE producto_codigo LIKE '%$busqueda%' OR producto_nombre LIKE '%$busqueda%'";

	}elseif($categoria_id>0){

		$consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id INNER JOIN usuario ON producto.usuario_id=usuario.usuario_id WHERE producto.categoria_id='$categoria_id' ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(producto_id) FROM producto WHERE categoria_id='$categoria_id'";

	}else{

		$consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id INNER JOIN usuario ON producto.usuario_id=usuario.usuario_id ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(producto_id) FROM producto";

	}

	$conexion=conexion();
	$datos = $conexion->query($consulta_datos);
	$datos = $datos->fetchAll();
	$total = $conexion->query($consulta_total);
	$total = (int) $total->fetchColumn();
	$Npaginas =ceil($total/$registros);
	$tabla.='
	<div class="table-container">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr class="has-text-centered">
                	<th>Codigo</th>
					<th>Marca</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th colspan="3">Opciones</th>
                </tr>
            </thead>
            <tbody>
			
	';

	if($total>=1 && $pagina<=$Npaginas){
		$contador=$inicio+1;
		$pag_inicio=$inicio+1;
		foreach($datos as $rows){

			$tabla.='
			
				<tr class="has-text-centered" >
					<td>'.substr(string: $rows['producto_codigo'],offset: 0,length: 25).'</td>
					<td>'.$rows['categoria_nombre'].'</td>
                    <td>'.$rows['producto_nombre'].'</td>
                   
                    <td>'.substr(  string: $rows['producto_stock'],offset: 0,length: 25).'<td>
                       
			            </div>
			            <div class="has-text-centered">
			                <a href="index.php?vista=product_img&product_id_up='.$rows['producto_id'].'" class="button is-link is-rounded is-small">Imagen</a>
			                <a href="index.php?vista=product_update&product_id_up='.$rows['producto_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
			                <a href="'.$url.$pagina.'&product_id_del='.$rows['producto_id'].'" class="button is-danger is-rounded is-small">Eliminar</a>
			         
						</div>

					</td>	
			        
			    </tr>

			    

				
            ';
			
			$contador++;
			
		}
		
		$pag_final=$contador-1;
	}else{
		if($total>=1){
			$tabla.='
				<tr class="has-text-centered" >
					<td colspan="5">
						<a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
							Haga clic acá para recargar el listado
						</a>
					</td>
				</tr>
				
			';
		}else{
			$tabla.='
				<tr class="has-text-centered" >
					<td colspan="6">
				 No hay registros en el sistema
					</td>
				</tr>
			';
		}
		
	}
	
	$tabla.='</tbody></table></div>';
	

	if($total>0 && $pagina<=$Npaginas){
		$tabla.='<p class="has-text-right">Mostrando Más <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
	}

	$conexion=null;
	echo $tabla;

	if($total>=1 && $pagina<=$Npaginas){
		echo paginador_tablas(pagina: $pagina,Npaginas: $Npaginas,url: $url,botones: 7);
	}
	