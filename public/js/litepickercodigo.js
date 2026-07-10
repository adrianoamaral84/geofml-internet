window.disableLitepickerStyles = true;
const picker = new Litepicker({ 
    element: document.getElementById('peridoinicial'),
    elementEnd: document.getElementById('final'),
    plugins: ['mobilefriendly','keyboardnav'],
    
    keyboardnav: {
        firstTabIndex: 2,
    },

    mobilefriendly: {
        breakpoint: 480,
    },

    dropdowns: {
        "minYear":2020,
        "maxYear":2022,
        "months":false,
        "years":false,
    },

    singleMode: false,
    allowRepick: false,
    numberOfMonths: 2,
    autoRefresh: true,

    disallowLockDaysInRange: true,

    format: "DD-MM-YYYY",
    inlineMode: true,
    lang: "pt-BR",
    //maxDays: 2,
    numberOfColumns: 2,
    
    maxDate: "{{ $maxDate }}",
    minDate: "{{ $minDate }}",

    lockDays: [['2021-04-01', '2021-04-05'],'2020-01-31'],
    tooltipText: {"one":"diária","other":"diarias"},
    
    tooltipNumber: (totalDays) => {
        return totalDays - 1;
    },

});
