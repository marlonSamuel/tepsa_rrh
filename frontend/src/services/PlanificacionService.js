class PlanificacionService{
    axios
    baseUrl

    constructor(axios,baseUrl){
        this.axios = axios
        this.baseUrl = `${baseUrl}planificaciones`
    }

    getAll(){
        let self = this
        return self.axios.get(`${self.baseUrl}`)
    }

    search(date,buque_id){
        let self = this
        return self.axios.get(`${self.baseUrl}_search/${date}/${buque_id}`)
    }
}

export default PlanificacionService