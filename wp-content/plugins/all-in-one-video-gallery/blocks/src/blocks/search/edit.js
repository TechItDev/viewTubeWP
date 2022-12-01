// Components
const { serverSideRender: ServerSideRender } = wp;

const {
	Disabled,
	PanelBody,
	SelectControl,
	ToggleControl	
} = wp.components;

const { 
	Component,
	Fragment
} = wp.element;

const {	InspectorControls } = wp.blockEditor;

/**
 * Create an AIOVGSearchEdit Component.
 */
class AIOVGSearchEdit extends Component {

	constructor() {
		super( ...arguments );		
		this.toggleAttribute = this.toggleAttribute.bind( this );
	}

	toggleAttribute( attribute ) {
		return ( newValue ) => {
			this.props.setAttributes( { [ attribute ]: newValue } );
		};
	}

	render() {		
		const { 
			attributes, 
			setAttributes 
		} = this.props;

		const { 
			template, 
			keyword,
			category,
			tag 
		} = attributes;

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title={ aiovg_blocks.i18n.general_settings }>
						<SelectControl
							label={ aiovg_blocks.i18n.select_template }
							value={ template }
							options={ [
								{ 
									label: aiovg_blocks.i18n.vertical, 
									value: 'vertical' 
								},
								{ 
									label: aiovg_blocks.i18n.horizontal, 
									value: 'horizontal' 
								}
							] }
							onChange={ ( value ) => setAttributes( { template: value } ) }
						/>
	
						<ToggleControl
							label={ aiovg_blocks.i18n.search_by_keywords }
							checked={ keyword }
							onChange={ this.toggleAttribute( 'keyword' ) }
						/>

						<ToggleControl
							label={ aiovg_blocks.i18n.search_by_categories }
							checked={ category }
							onChange={ this.toggleAttribute( 'category' ) }
						/>

						<ToggleControl
							label={ aiovg_blocks.i18n.search_by_tags }
							checked={ tag }
							onChange={ this.toggleAttribute( 'tag' ) }
						/>
					</PanelBody>
				</InspectorControls>

				<Disabled>
					<ServerSideRender
						block="aiovg/search"
						attributes={ attributes }
					/>
				</Disabled>
			</Fragment>
		);
	}	

}

export default AIOVGSearchEdit;