<?php

/**
 * Fichero que contiene las coordenadas de las cinco sedes, y los tiempos aproximados entre dos sedes por carretera
 */

/**Definimos ubicaciones con coordenadas (latitud, longitud)
 * Para usar con la función calcularDistancia(coordenadas1, coordenadas2) a través de la API de OSRM
 * Ventajas: se calcula en tiempo real, siempre está actualizado
 * Desventajas: el tiempo de espera para realizar el cálculo. No se puede usar en InfinityFree
 */
$ubicaciones = [
    'Madrid' => [40.4117, -3.7001],
    'Zaragoza' => [41.6469, -0.8896],
    'Albacete' => [38.9945, -1.8616],
    'León' => [42.5979, -5.5733],
    'Sevilla' => [37.3884, -5.9828]
];

/**Definimos las distancias fijas entre las ubicaciones, en km.
 * Para usar con la función calcularDistancia(origen, destino, distancias)
 * Ventajas: el cálculo es más rápido. Se puede usar en InfinityFree
 * Desventajas: no se actuliza (cortes de carreteras, carreteras nuevas, etc)
 */
// //$distancias = [
//     'Madrid-Zaragoza' => 322,
//     'Madrid-Albacete' => 258,
//     'Madrid-León' => 340,
//     'Madrid-Sevilla' => 533,
//     'Zaragoza-Albacete' => 398,
//     'Zaragoza-León' => 472,
//     'Zaragoza-Sevilla' => 834,
//     'Albacete-León' => 595,
//     'Albacete-Sevilla' => 519,
//     'León-Sevilla' => 670
// ];



//Definimos tiempos estimados entre dos destinos por carretera (en minutos)
// $tiempos_estimados = [
//     'Madrid-Zaragoza' => 240, // 4 horas
//     'Madrid-Albacete' => 210, // 3 horas 30 minutos
//     'Madrid-León' => 255, // 4 horas 15 minutos
//     'Madrid-Sevilla' => 385, // 6 horas 25 minutos
//     'Zaragoza-Albacete' => 355, //5h 55min
//     'Zaragoza-León' => 345, //5h 45min 
//     'Zaragoza-Sevilla' => 600, //10h
//     'Albacete-León' => 430, //7h 10min
//     'Albacete-Sevilla' => 375, //6h 15min 
//     'León-Sevilla' => 540 //8 horas
// ];
