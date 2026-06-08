import { Controller } from "react-hook-form";
import { 
    __experimentalVStack as VStack,
    __experimentalHStack as HStack,
    __experimentalText as Text, 
    ToggleControl,
    SelectControl,
    __experimentalInputControl as InputControl,
    ExternalLink,
    Card,
    CardHeader,  
    CardBody,
} from "@wordpress/components";
import { useState, useEffect } from "@wordpress/element";

import { __ } from '@wordpress/i18n';
import { SettingsCardSection, OptionsTable } from "../components";
import { FIELD_RELATIONS } from "../data/config";

function ConditionalLogic({control, reset, watch, setValue, activeID, fieldsData, productID, globalFields}) {
    const [ otherFields, setOtherFields ] = useState([]);
    const toBoolean = (value) => value === 'yes' || value === true;

    const globalConditions = [
        { label: __('Product', 'product-input-fields-for-woocommerce'), value: 'type_conditional_options_products' },
        { label: __('Product Category', 'product-input-fields-for-woocommerce'), value: 'type_conditional_options_categories' },
        { label: __('Product Tags', 'product-input-fields-for-woocommerce'), value: 'type_conditional_options_tags' },
        { label: __('Product Variation', 'product-input-fields-for-woocommerce'), value: 'type_conditional_options_variations' }
    ]

    const getRelationsForField = ( fieldType ) => {

        switch (fieldType) {
            case 'type_conditional_options_products':
                return FIELD_RELATIONS['products'];
            case 'type_conditional_options_categories':
                return FIELD_RELATIONS['categories'];
            case 'type_conditional_options_tags':
                return FIELD_RELATIONS['tags'];
            case 'type_conditional_options_variations':
                return FIELD_RELATIONS['variations'];
            default:
                const type = fieldType.split('_')[0];
                return FIELD_RELATIONS[type] || [];
        }
    };

    return (
        <VStack className={'pif_setting_section'} spacing={10} style={{margin: '30px'}}>
            <Text size={'14px'}>
                <ExternalLink href="https://www.tychesoftwares.com/products/woocommerce-product-input-fields-plugin/?utm_source=pifupgradetopro&utm_medium=link&utm_campaign=ProductInputFieldsLite" style={{ fontWeight: 'bold'}}>
                   { __( 'Upgrade to Pro' ) }
                </ExternalLink>
                { __( ' to control field visibility with conditions — show or hide any field based on other field values, user roles, or custom logic.' ) }
            </Text>
            <SettingsCardSection
                heading={ __( 'Conditional Logic', 'product-input-fields-for-woocommerce' ) }
                subHeading={ __( 'Control when this field appears based on products, categories, tags, variations, or other field values.', 'product-input-fields-for-woocommerce' ) }
                control={ control }
                className="pif-field-builder"
                fields={ [
                    {
                        name: 'enabled_conditional_logic',
                        defaultValue: '',
                        render: ( field ) => (
                            <ToggleControl
                                checked={ toBoolean(field.value) }
                                onChange={ field.onChange }
                                label={ __( 'Enable Conditional logic for the input fields', 'product-input-fields-for-woocommerce' ) }
                                help={__('Show or hide this field based on conditions.', 'product-input-fields-for-woocommerce')}
                                disabled={true}
                            />
                        ),
                    },
                ] }
            />

            <Card className="pif-field-builder">
                <CardHeader>
                    <HStack justify="left">
                        <Controller
                            name={`custom_show_hide`}
                            control={control}
                            defaultValue="show"
                            render={({ field }) => (
                                <SelectControl
                                    value={field.value}
                                    options={[
                                        { label: __('Show', 'product-input-fields-for-woocommerce'), value: 'show' },
                                        { label: __('Hide', 'product-input-fields-for-woocommerce'), value: 'hide' },
                                    ]}
                                    onChange={(value) => {
                                        field.onChange(value);
                                    }}
                                    __nextHasNoMarginBottom
                                    disabled={true}
                                />
                            )}
                        />
                        <Text>{ __( 'this field if ', 'product-input-fields-for-woocommerce') }</Text>
                        <Controller
                            name={`custom_match_type`}
                            control={control}
                            defaultValue="all"
                            render={({ field }) => (
                                <SelectControl
                                    value={field.value}
                                    options={[
                                        { label: __('All', 'product-input-fields-for-woocommerce'), value: 'all' },
                                        { label: __('Any', 'product-input-fields-for-woocommerce'), value: 'any' },
                                    ]}
                                    onChange={(value) => {
                                        field.onChange(value);
                                    }}
                                    __nextHasNoMarginBottom
                                    disabled={true}
                                />
                            )}
                        />
                        <Text>{ __( 'of the following conditions match:', 'product-input-fields-for-woocommerce') }</Text>
                    </HStack>
                </CardHeader>
                <CardBody>
                    <OptionsTable
                        id="type_conditional_options"
                        control={ control }
                        columns={ ['Condition Type', 'Relation', 'Value', 'Action'] }
                        templateColumns={'2fr 1fr 2fr 0.5fr'}
                        optionFields={ [
                            {
                                name: 'type_conditional_options_condition',
                                defaultValue: '',
                                render: ( field, index ) => {

                                    const options = [
                                        ...otherFields,
                                        ...(!productID ? globalConditions : []),
                                    ];

                                    return (
                                        <SelectControl
                                            label={''}
                                            value={field.value && field.value !== '' ? field.value : options[0]?.value}
                                            options={options}
                                            onChange={(value) => {
                                                field.onChange(value);
                                                // reset value field
                                                setValue(`type_conditional_options.${index}.type_conditional_options_value`, '');
                                            }}
                                            __nextHasNoMarginBottom
                                            disabled={true}
                                        />
                                    )
                                },
                            },
                            {
                                name: 'type_conditional_options_relation',
                                defaultValue: '',
                                render: ( field , index) => {
                                    const conditionValue = watch(`type_conditional_options.${index}.type_conditional_options_condition`) || ( otherFields.length > 0 ? otherFields[0].value : 'type_conditional_options_products' );

                                    const options = getRelationsForField(conditionValue);
                                    const isValidValue = options.some(opt => opt.value === field.value);

                                    useEffect(() => {
                                        if ((!field.value || !isValidValue) && options.length > 0) {
                                            field.onChange(options[0].value);
                                        }
                                    }, [options, field.value]);
                                    
                                    return (
                                        <SelectControl
                                            label={''}
                                            value={ field.value || options[0]?.value }
                                            options={ options }
                                            onChange={(value) => {
                                                field.onChange(value);
                                            }}
                                            __nextHasNoMarginBottom
                                            disabled={true}
                                        />
                                    )
                                },
                            },
                            {
                                name: 'type_conditional_options_value',
                                defaultValue: '',
                                render: ( field, index ) => {
                                    const conditionType = watch(
                                        `type_conditional_options.${index}.type_conditional_options_condition`
                                    ) || 'type_conditional_options_products';
                                    
                                    return (
                                        <InputControl
                                            value={field.value}
                                            onChange={(value) => field.onChange(value)}
                                            disabled={true}
                                        />
                                    )
                                },
                            },
                        ] }
                        defaultValue={ 'type_conditional_options_products' }
                    />
                </CardBody>
            </Card>
        </VStack>
    )

}

export default ConditionalLogic;