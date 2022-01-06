import axios from "axios";

let ApiBase = axios.create({
  baseURL: "internal/"
});

ApiBase.defaults.withCredentials = true;

export default ApiBase;
