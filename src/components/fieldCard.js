import { 
	Card, 
	CardBody, 
	Button, 
	Icon,
	__experimentalVStack as VStack,
    __experimentalHStack as HStack,
	__experimentalHeading as Heading, 
	__experimentalText as Text, 
	__experimentalGrid as Grid,
} from '@wordpress/components';
import { 
	upload, 
	chevronDown, 
	copy, 
	trash,
	dragHandle,
	menu 
  } from '@wordpress/icons';

const FieldCard = ({
	title,
	subtitle,
	icon,
	onCopy,
	onDelete,
	onClick,
	className = ''
}) => {
	return (
		<Card className={`field-card ${className}`} onClick={onClick} style={{cursor: 'pointer'}}>
			<CardBody style={{ padding: '10px 0'}}>
				<Grid columns={3} templateColumns={'0.5fr 2fr 0.5fr'} templateRows={'auto'} columnGap={4}>
					<div style={{alignContent: 'center'}}>
						<Icon icon={dragHandle} size={20} style={{fill: '#99a1af'}}/>
					</div>
					<div style={{margin: '10px 0'}}>
						<VStack align="start" spacing={1}>
							<Heading level={'4'}>{title}</Heading>	
							{subtitle && (
									<HStack justify='start' spacing={2}>
										<Icon icon={icon} size={15} className='field_type-icon' />
										<Text variant="secondary" style={{ color: '#4a5565'}}>{subtitle}</Text>
									</HStack>
							)}
						</VStack>

					</div>
					<div className="field-card__actions">
						<Button
								icon={copy}
								label='Copy'
								onClick={onCopy}
								iconSize='18'
								className='field-card__action-button'
							/>
							<Button
								icon={trash}
								label='Delete'
								onClick={onDelete}
								iconSize='18'
								isDestructive
								className='field-card__action-button'
							/>
					</div>
					
				</Grid>
			</CardBody>
		</Card>
	);
};

export default FieldCard;
