let boton = document.getElementById("boton");
boton.addEventListener("click", traerDatos);

function traerDatos(){
    let dni = document.getElementById("dni").value;
    fetch("https://apiperu.dev/api/dni/" + dni+"?api_token=1fd138e9c20b299c25169bcf566974014f173f7d11bfe655e43cc85faecb9e30")
    .then((datos)=>datos.json())
    .then((datos)=>{
        console.log(datos.data)
        document.getElementById("doc").value=datos.data.numero
        document.getElementById("nombre").value=datos.data.nombre
        document.getElementById("apellido").value=datos.data.apellido_paterno + " " + datos.data.apellido_materno
    })
}
