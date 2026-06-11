import { useForm, Controller } from "react-hook-form";
import { 
    __experimentalVStack as VStack,
    __experimentalHStack as HStack,
    __experimentalHeading as Heading, 
    __experimentalText as Text, 
    __experimentalDivider as Divider,
    Button,
    ToggleControl,
    __experimentalNumberControl as NumberControl,
    __experimentalInputControl as InputControl,
    SelectControl,
    RadioControl,
    FormFileUpload,
    DropZone,
    Card,
    CardHeader,  
    CardBody,
    CardFooter,
    TabPanel,
    TextareaControl,
    Icon,
    ExternalLink,
} from "@wordpress/components";

import { __ } from '@wordpress/i18n';
import { SettingsCardSection, OptionsTable, ImagePreview } from "../components";
import { trash, warning } from '@wordpress/icons';

function FieldSettings({control, reset, watch, getValues }) {
    const toBoolean = (value) => value === 'yes' || value === true;
    const type = watch('type');
    const pricing = toBoolean(watch('required_price'));

    return (
        <VStack className={'pif_setting_section'} spacing={10} style={{margin: '30px'}}>
            <SettingsCardSection
                heading={ __( 'Basic Settings', 'product-input-fields-for-woocommerce' ) }
                control={ control }
                className="pif-field-builder"
                fields={ [
                    {
                        name: 'type',
                        defaultValue: 'text',
                        label: __( 'Field Type', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <SelectControl
                                label={''}
                                value={field.value}
                                options={[
                                    { label: __('Text', 'product-input-fields-for-woocommerce'), value: 'text' },
                                    { label: __('Textarea', 'product-input-fields-for-woocommerce'), value: 'textarea' },
                                    { label: __('Number', 'product-input-fields-for-woocommerce'), value: 'number' },
                                    { label: __('Checkbox', 'product-input-fields-for-woocommerce'), value: 'checkbox' },
                                    { label: __('Color', 'product-input-fields-for-woocommerce'), value: 'color' },
                                    { label: __('File', 'product-input-fields-for-woocommerce'), value: 'file' },
                                    { label: __('Datepicker', 'product-input-fields-for-woocommerce'), value: 'datepicker' },
                                    { label: __('Weekpicker', 'product-input-fields-for-woocommerce'), value: 'weekpicker' },
                                    { label: __('Timepicker', 'product-input-fields-for-woocommerce'), value: 'timepicker' },
                                    { label: __('Select', 'product-input-fields-for-woocommerce'), value: 'select' },
                                    { label: __('Radio', 'product-input-fields-for-woocommerce'), value: 'radio' },
                                    { label: __('Password', 'product-input-fields-for-woocommerce'), value: 'password' },
                                    { label: __('Country', 'product-input-fields-for-woocommerce'), value: 'country' },
                                    { label: __('Email', 'product-input-fields-for-woocommerce'), value: 'email' },
                                    { label: __('Phone', 'product-input-fields-for-woocommerce'), value: 'tel' },
                                    { label: __('Search', 'product-input-fields-for-woocommerce'), value: 'search' },
                                    { label: __('URL', 'product-input-fields-for-woocommerce'), value: 'url' },
                                    { label: __('Range', 'product-input-fields-for-woocommerce'), value: 'range' },
                                ]}
                                onChange={(value) => {
                                    field.onChange(value);
                                }}
                                __nextHasNoMarginBottom
                            />
                        ),
                    },
                    {
                        name: 'title',
                        defaultValue: '',
                        label: __( 'Field Label', 'product-input-fields-for-woocommerce' ),
                        rules: {required: true},
                        render: ( field, error ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                placeholder="Enter field label"
                                className={error && 'show_error'}
                            />
                        ),
                    },
                    {
                        name: 'placeholder',
                        defaultValue: '',
                        label: __( 'Placeholder Text', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                placeholder="Enter placeholder text"
                                help={__('Hint text shown inside the field when empty.', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'default_value',
                        defaultValue: '',
                        label: __( 'Default Value', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                help={__('Pre-filled value when the page loads.', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'required_message',
                        defaultValue: 'Field "%title%" is required!',
                        label: __( 'Required Field Message', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                help={__('Message displayed when the required field is left empty.', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'class',
                        defaultValue: '',
                        label: __( 'Custom CSS Class', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                placeholder="e.g., my-custom-class"
                                help={__('Add custom CSS classes for styling (optional).', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'style',
                        defaultValue: '',
                        label: __( 'Style', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                placeholder=""
                                help={__('Add custom CSS for styling (optional).', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                ] }
            />

            <SettingsCardSection
                heading={ __( 'Input Restrictions', 'product-input-fields-for-woocommerce' ) }
                control={ control }
                className="pif-field-builder"
                display={ type === 'text' || type === 'textarea' || type === 'email' || type === 'url' || type === 'tel' || type === 'search' || type === 'password' || type === 'number' || type === 'range' }
                fields={ [
                    {
                        name: 'input_restrictions_min',
                        defaultValue: '',
                        label: __( 'Min Value', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'number' || type === 'range',
                        render: ( field ) => (
                            <NumberControl
                                value={ field.value }
                                onChange={ field.onChange }
                                help={__('Minimum value for an input field. E.g. for Number/Range type. Leave blank to disable', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'input_restrictions_max',
                        defaultValue: '',
                        label: __( 'Max Value', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'number' || type === 'range',
                        render: ( field ) => (
                            <NumberControl
                                value={ field.value }
                                onChange={ field.onChange }
                                help={__('Maximum value for an input field. E.g. for Number/Range type. Leave blank to disable', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'input_restrictions_step',
                        defaultValue: '',
                        label: __( 'Step', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'number' || type === 'range',
                        render: ( field ) => (
                            <NumberControl
                                value={ field.value }
                                onChange={ field.onChange }
                                help={__('Legal number intervals for an input field. E.g. for Number/Range type. Leave blank to disable', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'input_restrictions_maxlength',
                        defaultValue: '',
                        label: __( 'Max Length', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'text' || type === 'textarea' || type === 'email' || type === 'url' || type === 'tel' || type === 'search' || type === 'password',
                        render: ( field ) => (
                            <NumberControl
                                value={ field.value }
                                onChange={ field.onChange }
                                help={__('Maximum number of character for an input field. Leave blank to disable', 'product-input-fields-for-woocommerce')}
                                placeholder={'255'}
                            />
                        ),
                    },
                    {
                        name: 'input_restrictions_pattern',
                        defaultValue: '',
                        label: __( 'Pattern', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'text' || type === 'textarea' || type === 'email' || type === 'url' || type === 'tel' || type === 'search' || type === 'password',
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                help={__('Regular expression to check the input value against. Leave blank to disable', 'product-input-fields-for-woocommerce')}
                                placeholder='e.g. [A-Za-z0-9]+'
                            />
                        ),
                    },
                    {
                        name: 'type_file_accept',
                        defaultValue: '',
                        label: __( 'Accepted File Types', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'file',
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={'.jpg,.jpeg,.png'}
                                help={__('E.g.: ".jpg,.jpeg,.png". Leave blank to accept all files.', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'type_file_wrong_type_msg',
                        defaultValue: '',
                        label: __( 'Message on Wrong File Type', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'file',
                        render: ( field ) => (
                            <TextareaControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={'Wrong file type!'}
                            />
                        ),
                    },
                    {
                        name: 'type_file_max_size',
                        defaultValue: '',
                        label: __( 'Max File Size', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'file',
                        render: ( field ) => (
                            <NumberControl
                                value={ field.value }
                                onChange={ field.onChange }
                                help={__('Set to zero to accept all files (in bytes).', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'type_file_max_size_msg',
                        defaultValue: '',
                        label: __( 'Message on Max File Size Exceeded', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'file',
                        render: ( field ) => (
                            <TextareaControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={'File is too big!'}
                            />
                        ),
                    },
                ] }
            />
  
            <SettingsCardSection
                heading={ __( 'File Upload Settings', 'product-input-fields-for-woocommerce' ) }
                subHeading={__('Configure file upload restrictions.', 'product-input-fields-for-woocommerce')}
                control={ control }
                display={ type === 'file' }
                className="pif-field-builder"
                fields={ [
                    {
                        name: 'type_file_accept',
                        defaultValue: '',
                        label: __( 'Accepted File Types', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'file',
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={'.jpg,.jpeg,.png'}
                                help={__('E.g.: ".jpg,.jpeg,.png". Leave blank to accept all files.', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'type_file_wrong_type_msg',
                        defaultValue: '',
                        label: __( 'Message on Wrong File Type', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'file',
                        render: ( field ) => (
                            <TextareaControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={'Wrong file type!'}
                            />
                        ),
                    },
                    {
                        name: 'type_file_max_size',
                        defaultValue: '',
                        label: __( 'Max File Size', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'file',
                        render: ( field ) => (
                            <NumberControl
                                value={ field.value }
                                onChange={ field.onChange }
                                help={__('Set to zero to accept all files (in bytes).', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'type_file_max_size_msg',
                        defaultValue: '',
                        label: __( 'Message on Max File Size Exceeded', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'file',
                        render: ( field ) => (
                            <TextareaControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={'File is too big!'}
                            />
                        ),
                    },
                ] }
            />

            <SettingsCardSection
                heading={ __( 'Datepicker/Weekpicker Settings', 'product-input-fields-for-woocommerce' ) }
                subHeading={__('Configure date selection options.', 'product-input-fields-for-woocommerce')}
                control={ control }
                display={ type === 'datepicker' || type === 'weekpicker' }
                className="pif-field-builder"
                fields={ [
                    {
                        name: 'type_datepicker_format',
                        defaultValue: '',
                        label: __( 'Date Format', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                placeholder="e.g., mm/dd/yy"
                                help={__('Leave blank to use your current WordPress format.', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'type_datepicker_mindate',
                        defaultValue: '',
                        label: __( 'Min Date', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <NumberControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={-365}
                                help={__('Days', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'type_datepicker_maxdate',
                        defaultValue: '',
                        label: __( 'Max Date', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <NumberControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={365}
                                help={__('Days', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'type_datepicker_addyear',
                        defaultValue: '',
                        label: __( 'Year Selector', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <ToggleControl
                                checked={ toBoolean(field.value) }
                                onChange={ field.onChange }
                                label={ __('Add Year Selector', 'product-input-fields-for-woocommerce' ) }
                                help={__('Enable to show Year Selector options below.', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'type_datepicker_yearrange',
                        defaultValue: '',
                        label: __( 'Year Selector - Year Range', 'product-input-fields-for-woocommerce' ),
                        showWhen: type === 'datepicker' && toBoolean(watch('type_datepicker_addyear')),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={'c-10:c+10'}
                                help={__('E.g., "c-10:c+10" for 10 years back and forward from current year. Remember to set "Min Date" and "Max Date" options accordingly', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                    {
                        name: 'type_datepicker_firstday',
                        defaultValue: '',
                        label: __( 'First Week Day', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <SelectControl
                                    label={''}
                                    value={field.value}
                                    options={[
                                        { label: __('Sunday', 'product-input-fields-for-woocommerce'), value: '0' },
                                        { label: __('Monday', 'product-input-fields-for-woocommerce'), value: '1' },
                                        { label: __('Tuesday', 'product-input-fields-for-woocommerce'), value: '2' },
                                        { label: __('Wednesday', 'product-input-fields-for-woocommerce'), value: '3' },
                                        { label: __('Thursday', 'product-input-fields-for-woocommerce'), value: '4' },
                                        { label: __('Friday', 'product-input-fields-for-woocommerce'), value: '5' },
                                        { label: __('Saturday', 'product-input-fields-for-woocommerce'), value: '6' },
                                    ]}
                                    onChange={(value) => {
                                        field.onChange(value);
                                    }}
                                    __nextHasNoMarginBottom
                            />
                        ),
                    },
                ] }
            />

            <SettingsCardSection
                heading={ __( 'Timepicker Settings', 'product-input-fields-for-woocommerce' ) }
                subHeading={__('Configure time selection options.', 'product-input-fields-for-woocommerce')}
                control={ control }
                display={ type === 'timepicker' }
                className="pif-field-builder"
                fields={ [
                    {
                        name: 'type_timepicker_format',
                        defaultValue: '',
                        label: __( 'Time Format', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={'hh:mm p'}
                            />
                        ),
                    },
                    {
                        name: 'type_timepicker_interval',
                        defaultValue: '',
                        label: __( 'Interval', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                                defaultValue={''}
                                help={__('Minutes', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                ] }
            />

            <SettingsCardSection
                heading={ __( 'Color Settings', 'product-input-fields-for-woocommerce' ) }
                subHeading={__('Configure color typing options.', 'product-input-fields-for-woocommerce')}
                control={ control }
                display={ type === 'color' }
                className="pif-field-builder"
                fields={ [
                    {
                        name: 'type_color_allow_typing',
                        defaultValue: '',
                        render: ( field ) => (
                            <ToggleControl
                                checked={ toBoolean(field.value) }
                                onChange={ field.onChange }
                                label={ __('Allow color typing', 'product-input-fields-for-woocommerce' ) }
                                help={__('Allows typing or pasting the color manually.', 'product-input-fields-for-woocommerce')}
                            />
                        ),
                    },
                ] }
            />

            <SettingsCardSection
                heading={ __( 'Field Pricing', 'product-input-fields-for-woocommerce' ) }
                subHeading={ 
                    <>
                        {__( 'Configure pricing options for this field. ', 'product-input-fields-for-woocommerce' )}
                        <ExternalLink href="https://www.tychesoftwares.com/products/woocommerce-product-input-fields-plugin/?utm_source=pifupgradetopro&utm_medium=link&utm_campaign=ProductInputFieldsLite" style={{ fontWeight: 'bold'}}>
                        { __( 'Upgrade to Pro' ) }
                        </ExternalLink>
                    </>
                 }
                control={ control }
                className="pif-field-builder"
                fields={ [
                    {
                        name: 'required_price',
                        defaultValue: false,
                        label: __( 'Add Extra Price', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <ToggleControl
                                checked={ toBoolean(field.value) }
                                onChange={ field.onChange }
                                label={ __( 'Enable pricing', 'product-input-fields-for-woocommerce' ) }
                                help={__('Enable pricing to add prices for the input fields.', 'product-input-fields-for-woocommerce')}
                                disabled={true}
                            />
                        ),
                    },
                ] }
            />

            {
                ( type === 'select' || type === 'radio' || type === 'multicheck' ) && (
                    <Card className="pif-field-builder">
                        <CardHeader>
                            <VStack spacing={ 2 }>
                                <Heading level={ 4 }>{ __( 'Field Options', 'product-input-fields-for-woocommerce' ) }</Heading>
                                    <Text className="components-text">
                                        { __( 'Add options for customers to choose from.', 'product-input-fields-for-woocommerce' ) }
                                    </Text>
                            </VStack>
                        </CardHeader>
                        <CardBody>
                            
                            <OptionsTable
                                id="type_select_options"
                                control={ control }
                                columns={ ['Option Label', 'Action']}
                                templateColumns={ '4fr 0.5fr'}
                                optionFields={ [
                                    {
                                        name: 'type_select_options_option',
                                        defaultValue: '',
                                        render: ( field ) => (
                                            <InputControl
                                                value={field.value}
                                                onChange={field.onChange}
                                                placeholder='Option Name'
                                            />
                                        ),
                                    },
                                ] }
                            />
                        </CardBody>
                    </Card>

                )
            }
            <SettingsCardSection
                heading={ __( 'Checkbox Type Options', 'product-input-fields-for-woocommerce' ) }
                subHeading={ __( 'Fill this only if Checkbox type is selected.', 'product-input-fields-for-woocommerce' ) }
                control={ control }
                display={ type === 'checkbox' }
                className="pif-field-builder"
                fields={ [
                    {
                        name: 'type_checkbox_yes',
                        defaultValue: 'Yes',
                        label: __( 'Value for ON', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                            />
                        ),
                    },
                    {
                        name: 'type_checkbox_no',
                        defaultValue: 'No',
                        label: __( 'Value for OFF', 'product-input-fields-for-woocommerce' ),
                        render: ( field ) => (
                            <InputControl
                                value={ field.value }
                                onChange={ field.onChange }
                            />
                        ),
                    },
                ] }
            />
        </VStack>
    )

}

export default FieldSettings;