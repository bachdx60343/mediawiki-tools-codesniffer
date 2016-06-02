<?php
/**
* Verify comments are preceeded by a single space.
*/
// @codingStandardsIgnoreStart
class MediaWiki_Sniffs_WhiteSpace_SpaceBeforeSingleLineCommentSniff
	implements PHP_CodeSniffer_Sniff {
	// @codingStandardsIgnoreEnd
	/**
	 * @return array
	 */
	public function register() {
		return [
			T_COMMENT
		];
	}

	/**
	 * @param  PHP_CodeSniffer_File $phpcsFile PHP_CodeSniffer_File object.
	 * @param  int $stackPtr The current token index.
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$currToken = $tokens[$stackPtr];
		if ( $currToken['code'] === T_COMMENT ) {
			// Accounting for multiple line comments, as single line comments
			// use only '//' and '#'
			// Also ignoring phpdoc comments starting with '///',
			// as there are no coding standards documented for these
			if ( substr( $currToken['content'], 0, 2 ) === '/*'
				|| substr( $currToken['content'], 0, 3 ) === '///'
			) {
				return;
			}

			// Checking whether the comment is an empty one
			if ( ( substr( $currToken['content'], 0, 2 ) === '//' &&
				rtrim( $currToken['content'] ) === '//' ) ||
				( $currToken['content'][0] === '#' &&
					rtrim( $currToken['content'] ) === '#' )
			) {
				$phpcsFile->addWarning( 'Unnecessary empty comment found',
					$stackPtr,
					'EmptyComment'
				);
			// Checking whether there is a space between the comment delimiter
			// and the comment
			} elseif ( substr( $currToken['content'], 0, 2 ) === '//' ) {
				$commentContent = substr( $currToken['content'], 2 );
				$commentTrim = ltrim( $commentContent );
				if ( strlen( $commentContent ) !== ( strlen( $commentTrim ) + 1 ) ||
					$currToken['content'][2] !== ' '
				) {
				$error = 'Single space expected between "//" and comment';
				$fix = $phpcsFile->addFixableWarning( $error, $stackPtr,
					'SingleSpaceBeforeSingleLineComment'
				);
				if ( $fix === true ) {
					$newContent = '// ';
					$newContent .= $commentTrim;
					$phpcsFile->fixer->replaceToken( $stackPtr, $newContent );
				}
				}
			// Finding what the comment delimiter is and checking whether there is a space
			// between the comment delimiter and the comment.
			} elseif ( $currToken['content'][0] === '#' ) {
				// Find number of `#` used.
				$startComment = 0;
				while ( $currToken['content'][$startComment] === '#' ) {
					$startComment += 1;
				}
				if ( $currToken['content'][$startComment] !== ' ' ) {
					$error = 'Single space expected between "#" and comment';
					$fix = $phpcsFile->addFixableWarning( $error, $stackPtr,
						'SingleSpaceBeforeSingleLineComment'
					);
					if ( $fix === true ) {
						$content = $currToken['content'];
						$newContent = '# ';
						$tmpContent = substr( $content, 1 );
						$newContent .= ltrim( $tmpContent );
						$phpcsFile->fixer->replaceToken( $stackPtr, $newContent );
					}
				}
			}
		}
	}
}
