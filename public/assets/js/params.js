// Defines the style of the neighborhoods' polygons
function style(feature) {
    return {
        fillColor: '#0000FF',
        weight: 1,
        opacity: 1,
        color: 'black',
        dashArray: '3',
        fillOpacity: 0.3
    };
}

// Defines the style of the neighborhoods' polygons
function filter(feature) {
    if (feature.properties.code === "31") {
        return true;
    }
}