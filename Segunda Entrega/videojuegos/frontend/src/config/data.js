const BASE_URL = 'http://localhost/'

const realizarSolicitud = async (url, metodo = "GET", data = null, token = null, esJson = true) => {
  try {
    // Construcción de opciones para fetch
    const opciones = {
      method: metodo,
      headers: {
        ...(esJson ? { "Content-Type": "application/json" } : {}),
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
    };

    // Configuración del cuerpo según el tipo de datos
    if (data) {
      opciones.body = esJson ? JSON.stringify(data) : data;
    }

    const resp = await fetch(url, opciones);
    const responseData = await resp.json();
    console.log(responseData)
    return responseData;
  } catch (error) {
    console.error("Error en la solicitud: ", error);
    return { Codigo: 500, Mensaje: "Lo sentimos, el servidor no esta disponible en este momento. Por favor, intenta de nuevo mas tarde." };
  }
};

export { realizarSolicitud, BASE_URL };
