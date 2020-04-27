# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [Unreleased][unreleased]

See <https://commonmark.thephpleague.com/2.0/upgrading/> for detailed information on upgrading to version 2.0.

### Added

 - Added new `HtmlFilter` and `StringContainerHelper` utility classes
 - Added new `AbstractBlockParser` class to simplify the creation of custom block parsers
 - Added several new classes and interfaces:
   - `BlockContinue`
   - `BlockParserFactoryInterface`
   - `BlockStart`
   - `CursorState`
   - `DocumentBlockParser`
   - `InlineParserEngineInterface`
   - `DocParserState` and `DocParserStateParserInterface`
 - Added several new methods:
   - `FencedCode::setInfo()`
   - `LinkParserHelper::parsePartialLinkLabel()`
   - `LinkParserHelper::parsePartialLinkTitle()`
   - `RegexHelper::isLetter()`
   - `StringContainerInterface::setLiteral()`
   - `TableCell::getType()`
   - `TableCell::setType()`
   - `TableCell::getAlign()`
   - `TableCell::setAlign()`

### Changed

 - Moved classes into different namespaces ([full list here](https://commonmark.thephpleague.com/2.0/upgrading/#classesnamespaces-renamed))
 - Implemented a new approach to block parsing. This was a massive change, so here are the highlights:
   - Functionality previous in block parsers and node elements has moved to block parser factories and block parsers, respectively ([more details](https://commonmark.thephpleague.com/2.0/upgrading/#new-block-parsing-approach))
   - `ConfigurableEnvironmentInterface::addBlockParser()` is now `ConfigurableEnvironmentInterface::addBlockParserFactory()`
   - `ReferenceParser` was re-implemented and works completely different than before
   - The paragraph parser no longer needs to be added manually to the environment
 - Renamed the following classes:
   - `ElementRendererInterface` is now `NodeRendererInterface`
   - `LazyParagraphParser` is now `ParagraphParser`
 - Renamed the following methods:
   - `ReferenceMap` and `ReferenceMapInterface`:
     - `addReference()` is now `add()`
     - `getReference()` is now `get()`
     - `listReferences()` is now `getIterator()`
   - Various node (block/inline) classes:
     - `getContent()` is now `getLiteral()`
     - `setContent()` is now `setLiteral()`
 - Moved and renamed the following constants:
   - `EnvironmentInterface::HTML_INPUT_ALLOW` is now `HtmlFilter::ALLOW`
   - `EnvironmentInterface::HTML_INPUT_ESCAPE` is now `HtmlFilter::ESCAPE`
   - `EnvironmentInterface::HTML_INPUT_STRIP` is now `HtmlFilter::STRIP`
 - Added missing return types to virtually every class and interface method
 - Several methods which previously returned `$this` now return `void`
   - `Delimiter::setPrevious()`
   - `Node::replaceChildren()`
   - `Context::setTip()`
   - `Context::setContainer()`
   - `Context::setBlocksParsed()`
   - `AbstractStringContainer::setContent()`
   - `AbstractWebResource::setUrl()`
 - `Heading` nodes no longer directly contain a copy of their inner text
 - `StringContainerInterface` can now be used for inlines, not just blocks
 - `Cursor::saveState()` and `Cursor::restoreState()` now use `CursorState` objects instead of arrays

### Removed

 - Removed support for PHP 7.1
 - Removed all previously-deprecated functionality:
   - Removed the `Converter` class and `ConverterInterface`
   - Removed the `bin/commonmark` script
   - Removed the `Html5Entities` utility class
   - Removed the following `ArrayCollection` methods:
     - `add()`
     - `set()`
     - `get()`
     - `remove()`
     - `isEmpty()`
     - `contains()`
     - `indexOf()`
     - `containsKey()`
     - `replaceWith()`
     - `removeGaps()`
   - Removed the `ListBlock::TYPE_UNORDERED` constant
 - Removed now-unused classes:
   - `AbstractStringContainerBlock`
   - `Context`
   - `ContextInterface`
   - `Converter`
   - `ConverterInterface`
   - `UnmatchedBlockCloser`
 - Removed the following methods and members:
   - `AbstractBlock::$open`
   - `AbstractBlock::$lastLineBlank`
   - `AbstractBlock::isContainer()`
   - `AbstractBlock::canContain()`
   - `AbstractBlock::isCode()`
   - `AbstractBlock::matchesNextLine()`
   - `AbstractBlock::endsWithBlankLine()`
   - `AbstractBlock::setLastLineBlank()`
   - `AbstractBlock::shouldLastLineBeBlank()`
   - `AbstractBlock::isOpen()`
   - `AbstractBlock::finalize()`
   - `ConfigurableEnvironmentInterface::addBlockParser()`
   - `Delimiter::setCanClose()`
   - `Node::isContainer()`
 - Removed the second `$contents` argument from the `Heading` constructor

[unreleased]: https://github.com/thephpleague/commonmark/compare/1.4...master
