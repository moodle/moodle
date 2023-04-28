import {reactiveInstance} from 'local_qubitsreaactiveui/reactive';
import PythonComponent from 'local_qubitsreaactiveui/pythoncomponent';


export const init = (domElementId) => {
    return new YourComponent({
        element: document.getElementById(domElementId),
        reactive: reactiveInstance    
    });
}