import imgNotFound from "../assets/images/not-found.jpg";


const isValidBase64 = (str) => {
  const base64Pattern =
    /^data:image\/(jpeg|png|gif);base64,[A-Za-z0-9+/]+={0,2}$/;
  return base64Pattern.test(str);
};

const imagenValida = (imagen) => {
  if (isValidBase64(imagen)) {
    return imagen;
  }
  return imgNotFound;
};

export { imagenValida };