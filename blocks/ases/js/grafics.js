$(document).ready(function() {
    
    $(this).on("click","input.edad",function() {

            $('#myModalLabel').html("<b> GRÁFICA POR EDAD </b>");
            
            var comboBox =  document.getElementById("cohorte");
            var selected = comboBox.selectedIndex;
            var cohorte =comboBox[selected].text;
            
            
            var edades = [];
            var cantidadEstudiantes = [];
            var arregloGrafica=[];
            $.ajax({
                type: "POST",
                data: {type: "edad", cohort: cohorte},
                url: "../managers/grafics_processing.php",
                success: function(msg)
                {
                    console.log(msg);
                    edades = Object.keys(msg).sort();
                    cantidadEstudiantes = retornarCantidadEstudiantes(msg,edades);
                    arregloGrafica=combinarArreglos(edades,cantidadEstudiantes);
                },
                dataType: "json",
                async: false,
                cache: "false",
                error: function(msg){console.log("error")},
                });
            
            
            Highcharts.chart('myModalBody',  {
            chart: {
                type: 'column'
            },
            title: {
                text: 'GRÁFICA DE ESTUDIANTES POR EDADES'
            },
            xAxis: {
                type: 'category',
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                },title: {
                    text: 'edad'
                }
            },
            yAxis: {
                title: {
                    text: 'Cantidad estudiantes'
                }
            },
            legend: {
                enabled: false
            },
            series: [{
                name: "Cantidad",
                data: arregloGrafica
            }]
        });
    });
    
    $(this).on("click","input.sexo",function() {
        $('#myModalLabel').html("<b> GRÁFICA POR SEXO </b>");

    var comboBox =  document.getElementById("cohorte");
    var selected = comboBox.selectedIndex;
    var cohorte =comboBox[selected].text;

        var xHombres;
        var xMujeres;
        var arreglo = new Array();
        
        $.ajax({
        type: "POST",
        data: {type: "sexo", cohort: cohorte},
        url: "../managers/grafics_processing.php",
        success: function(msg)
        {
            arreglo = msg;
            xHombres = arreglo.M.count;
            xMujeres = arreglo.F.count;
            console.log(xHombres);
            console.log(xMujeres);
            
        },
        dataType: "json",
        async: false,
        cache: "false",
        error: function(msg){console.log("error")},
        });
            console.log("d: "+xHombres);
            console.log("d: "+xMujeres);
        
        var total = xHombres + xMujeres;
        var porcHombres = xHombres/(total);
        var porcMujeres = xMujeres/(total);

        Highcharts.chart('myModalBody', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 1,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Cantidad de estudiantes segun Sexo'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.x}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
                name: 'Cantidad',
                colorByPoint: true,
                data: [{
                    name: 'Hombres',
                    y: porcHombres,
                    x: xHombres
                },   {
                    name: 'Mujeres',
                    y: porcMujeres,
                    x: xMujeres
                }]
            }]
        });
    
    });
    
    $(this).on("click","input.carrera",function() {

    
    var comboBox =  document.getElementById("cohorte");
    var selected = comboBox.selectedIndex;
    var cohorte =comboBox[selected].text;
    
    $('#myModalLabel').html("<b> Gráfica por carrera. (" + cohorte + ") </b> ");
    
    var cantidad = [];
    var carrera = [];
    var arregloGrafica=[];
    $.ajax({
        type: "POST",
        data: {type: "carrera", cohort: cohorte},
        url: "../managers/grafics_processing.php",
        success: function(msg)
        {
            carrera= extraerDato(msg,"nombre");
            cantidad = extraerDato(msg,"count");
            arregloGrafica=combinarArreglos(carrera,cantidad);
            console.log(arregloGrafica);
            
        },
        dataType: "json",
        async: false,
        cache: "false",
        error: function(msg){console.log("error")},
        });
        
        
           Highcharts.chart('myModalBody',  {
    chart: {
        type: 'column'
        
    },
    title: {
        text: 'GRÁFICA DE ESTUDIANTES POR CARRERA'
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        },title: {
            text: 'Carreras'
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Cantidad estudiantes'
        }
    },
    legend: {
        enabled: false
    },
    series: [{
        name: "Cantidad",
        data: arregloGrafica
    }]
});
    
    });
    
    $(this).on("click","input.facultad",function() {
        $('#myModalLabel').html("<b> GRÁFICA POR FACULTAD </b>");
    
    var comboBox =  document.getElementById("cohorte");
    var selected = comboBox.selectedIndex;
    var cohorte =comboBox[selected].text;
    
    var carrera = [];
    var cantidad = [];
    var arregloGrafica=[];
    $.ajax({
        type: "POST",
        data: {type: "facultad", cohort: cohorte},
        url: "../managers/grafics_processing.php",
        success: function(msg)
        {
            carrera= extraerDato(msg,"nombre");
            cantidad = extraerDato(msg,"count");
            arregloGrafica=combinarArreglos(carrera,cantidad);
            console.log(arregloGrafica);
            
        },
        dataType: "json",
        async: false,
        cache: "false",
        error: function(msg){console.log("error")},
        });
    
    
    Highcharts.chart('myModalBody',  {
    chart: {
        type: 'column'
    },
    title: {
        text: 'GRÁFICA DE ESTUDIANTES POR FACULTAD'
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        },title: {
            text: 'Facultad'
        }
    },
    yAxis: {
        title: {
            text: 'Cantidad estudiantes'
        }
    },
    legend: {
        enabled: false
    },
    series: [{
        name: "Cantidad",
        color: "red",
        data: arregloGrafica
    }]
});
        
    });
    
    $(this).on("click","input.estado",function() {
        $('#myModalLabel').html("<b> GRÁFICA POR ESTADO </b>");
    var comboBox =  document.getElementById("cohorte");
    var selected = comboBox.selectedIndex;
    var cohorte =comboBox[selected].text;
    
    var cantidad = [];
    var carrera = [];
    var arregloGrafica=[];
    $.ajax({
        type: "POST",
        data: {type: "estado", cohort: cohorte},
        url: "../managers/grafics_processing.php",
        success: function(msg)
        {
            carrera= extraerDato(msg,"data");
            cantidad = extraerDato(msg,"count");
            arregloGrafica=combinarArreglos(carrera,cantidad);
            console.log(arregloGrafica);
            
        },
        dataType: "json",
        async: false,
        cache: "false",
        error: function(msg){console.log("error")},
        });
        
        
           Highcharts.chart('myModalBody',  {
    chart: {
        type: 'column'
    },
    title: {
        text: 'GRÁFICA DE ESTUDIANTES POR ESTADO'
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        },title: {
            text: 'Estado'
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Cantidad estudiantes'
        }
    },
    legend: {
        enabled: false
    },
    series: [{
        name: "Cantidad",
        data: arregloGrafica
    }]
});
        
    });
});

function retornarCantidadEstudiantes(arregloEdades,edades)
{
    var arregloRetornar=[];
    for(var edad in edades)
    {
        arregloRetornar.push(arregloEdades[edades[edad]]);
    }
    
    return arregloRetornar;
}

function extraerDato(arreglo,posicion)
{
    var arregloClaves=[];
    for(var subarreglo in arreglo)
    {
        arregloClaves.push(arreglo[subarreglo][posicion]);
    }
    return arregloClaves;
}

function combinarArreglos(arregloA,arregloB)
{
    var cantidad=0;
    var arregloRetornar=[];
    for(var cantidadPosiciones in arregloA)
    {
        cantidad=cantidad+parseInt(arregloB[cantidadPosiciones]);
        var arregloAux=[arregloA[cantidadPosiciones],parseInt(arregloB[cantidadPosiciones])];
        arregloRetornar.push(arregloAux)
    }
    console.log(cantidad)
    return arregloRetornar;
}
