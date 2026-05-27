import { 
	upload,
	textColor,
	plus,
	chevronDown,
	check,
	color,
	calendar,
	lockOutline,
	envelope,
	globe,
	mobile,
	search,
	link,
	arrowRight
} from '@wordpress/icons';

const EQUALITY_OPERATORS = [
    { value: 'equals', label: 'Equals', description: 'Value matches exactly' },
    { value: 'not_equals', label: 'Not equals', description: 'Value is anything except that value' },
];
  
const TEXT_OPERATORS = [
    ...EQUALITY_OPERATORS,
    { value: 'contains', label: 'Contains' },
    { value: 'starts_with', label: 'Starts with' },
    { value: 'ends_with', label: 'Ends with' },
];

export const FIELD_RELATIONS = {
    checkbox: [
      { value: 'is', label: 'Is' },
      { value: 'is_not', label: 'Is not' },
    ],

    multicheck: [
      { value: 'is', label: 'Is' },
      { value: 'is_not', label: 'Is not' },
    ],
  
    select: [
      ...EQUALITY_OPERATORS,
      { value: 'contains', label: 'Contains', description: 'Field includes a specific option (multi-select)' },
      { value: 'not_contains', label: 'Not contains', description: 'Field does not include that option' },
    ],
  
    radio: EQUALITY_OPERATORS,

    search: [
      ...EQUALITY_OPERATORS,
      { value: 'contains', label: 'Contains', description: 'Field includes a specific option (multi-select)' },
      { value: 'not_contains', label: 'Not contains', description: 'Field does not include that option' },
    ],

    url: [
      { value: 'contains', label: 'Contains', description: 'Field includes a specific option (multi-select)' },
      { value: 'not_contains', label: 'Not contains', description: 'Field does not include that option' },
    ],
  
    dropdown: [
      ...EQUALITY_OPERATORS,
      { value: 'contains', label: 'Contains', description: 'Field includes a specific option (multi-select)' },
      { value: 'not_contains', label: 'Not contains', description: 'Field does not include that option' },
    ],
  
    text: [
      { value: 'equals', label: 'Equals', description: 'Input matches exactly' },
      { value: 'not_equals', label: 'Not equals', description: 'Value is anything except that value' },
      { value: 'contains', label: 'Contains', description: 'Input includes certain characters' },
      { value: 'starts_with', label: 'Starts with', description: 'Text starts with certain characters' },
      { value: 'ends_with', label: 'Ends with', description: 'Text ends with certain characters' },
    ],
  
    textarea: TEXT_OPERATORS,
  
    color: EQUALITY_OPERATORS,
  
    email: [
      ...EQUALITY_OPERATORS,
      { value: 'contains', label: 'Contains' },
    ],
  
    datepicker: [
      ...EQUALITY_OPERATORS,
    ],

    timepicker: [
      ...EQUALITY_OPERATORS,
    ],

    weekpicker: [
      { value: 'includes', label: 'Includes', description: 'Range includes a specific value' },
      { value: 'no_includes', label: 'Not includes', description: 'Range does not include that value' },
    ],

    tel: [
      ...EQUALITY_OPERATORS,
    ],

    range: [
      { value: 'includes', label: 'Includes', description: 'Range includes a specific value' },
      { value: 'no_includes', label: 'Not includes', description: 'Range does not include that value' },
    ],
  
    number: [
      { value: 'greater_than', label: 'Greater than', description: 'Number is more than a specific value' },
      { value: 'less_than', label: 'Less than', description: 'Number is less than a specific value' },
      { value: 'equals', label: 'Equal to', description: 'Number equal to a specific value' },
    ],
  
    file: [
      { value: 'has_file', label: 'Has file' },
      { value: 'no_file', label: 'No file' },
    ],

    password: [
      { value: 'length_greater_than', label: 'Length greater than', description: 'Password length is more than a specific value' },
      { value: 'length_less_than', label: 'Length less than', description: 'Password length is less than a specific value' },
      { value: 'contains', label: 'Contains' },
      { value: 'not_contains', label: 'Not contains' },
    ],
  
    products: [
      { value: 'in_list', label: 'In list' },
      { value: 'not_in_list', label: 'Not in list' },
    ],
  
    categories: [
      { value: 'in_list', label: 'In list' },
      { value: 'not_in_list', label: 'Not in list' },
    ],
  
    tags: [
      { value: 'in_list', label: 'In list' },
      { value: 'not_in_list', label: 'Not in list' },
    ],
  
    variations: [
      { value: 'in_list', label: 'In list' },
      { value: 'not_in_list', label: 'Not in list' },
    ],

    country: [
      { value: 'in_list', label: 'In list' },
      { value: 'not_in_list', label: 'Not in list' },
    ],
};

export const FIELD_TYPES = {
    'text': {
        label: 'Text',
        icon: textColor 
    },
    'number': {
        label: 'Number',
        icon: plus 
    },
    'select': {
        label: 'Select',
        icon: chevronDown
    },
    'checkbox': {
        label: 'Checkbox',
        icon: check 
    },
    'radio': {
        label: 'Radio',
        icon: chevronDown
    },
    'multicheck': {
        label: 'Multi Checkbox',
        icon: check 
    },
    'file': {
        label: 'File Upload',
        icon: upload
    },
    'color': {
        label: 'Color',
        icon: color
    },
    'datepicker': {
        label: 'DatePicker',
        icon: calendar
    },
    'timepicker': { 
        label: 'TimePicker',
        icon: calendar 
    },
    'weekpicker': {
        label: 'WeekPicker',
        icon: calendar
    },
    'textarea': {
        label: 'Text Area',
        icon: textColor 
    },
    'password': {
        label: 'Password',
        icon: lockOutline
    },
    'email': {
        label: 'Email',
        icon: envelope 
    },
    'country': {
        label: 'Country',
        icon: globe 
    },
    'tel': {
        label: 'Phone',
        icon: mobile 
    },
    'search': {
        label: 'Search',
        icon: search 
    },
    'url': {
        label: 'URL',
        icon: link 
    },
    'range': {
        label: 'Range',
        icon: arrowRight 
    },
    
}