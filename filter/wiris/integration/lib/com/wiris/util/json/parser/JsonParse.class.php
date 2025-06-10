<?php

class com_wiris_util_json_parser_JsonParse {
	public function __construct() { 
	}
	static $ALLOW_SINGLE_QUOTES = true;
	static function parse($jsonString) {
		$stateStack = new _hx_array(array());
		$currentJType = null;
		$expectingComma = false;
		$expectingColon = false;
		$fieldStart = 0;
		$singleQuoteString = false;
		$end = strlen($jsonString) - 1;
		$i = 0;
		$propertyName = null;
		$currentContainer = null;
		$value = null;
		$current = null;
		try {
			while(com_wiris_util_json_parser_JsonParse::isWhitespace($current = com_wiris_system_Utf8::charValueAt($jsonString, $i))) {
				$i++;
			}
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				throw new HException(new com_wiris_system_Exception("Provided JSON string did not contain a value", null));
			}
		}
		if($current === 123) {
			$currentJType = com_wiris_util_json_parser_JType::$TYPE_OBJECT;
			$currentContainer = new Hash();
			$i++;
		} else {
			if($current === 91) {
				$currentJType = com_wiris_util_json_parser_JType::$TYPE_ARRAY;
				$currentContainer = new _hx_array(array());
				$propertyName = null;
				$i++;
			} else {
				if($current === 34 || com_wiris_util_json_parser_JsonParse::$ALLOW_SINGLE_QUOTES && $current === 39) {
					$currentJType = com_wiris_util_json_parser_JType::$TYPE_STRING;
					$singleQuoteString = $current === 39;
					$fieldStart = $i;
				} else {
					if(com_wiris_util_json_parser_JsonParse::isLetter($current)) {
						$currentJType = com_wiris_util_json_parser_JType::$TYPE_CONSTANT;
						$fieldStart = $i;
					} else {
						if(com_wiris_util_json_parser_JsonParse::isNumberStart($current)) {
							$currentJType = com_wiris_util_json_parser_JType::$TYPE_NUMBER;
							$fieldStart = $i;
						} else {
							throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "Unexpected character \"" . _hx_string_rec($current, "") . "\" instead of root value"), null));
						}
					}
				}
			}
		}
		while($i <= $end) {
			$current = com_wiris_system_Utf8::charValueAt($jsonString, $i);
			if($currentJType === com_wiris_util_json_parser_JType::$TYPE_NAME) {
				try {
					$extracted = com_wiris_util_json_parser_JsonParse::extractString($jsonString, $i, $singleQuoteString);
					$i = $extracted->sourceEnd;
					$propertyName = $extracted->str;
					$singleQuoteString = false;
					unset($extracted);
				}catch(Exception $»e) {
					$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
					$e2 = $_ex_;
					{
						throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "String did not have ending quote"), null));
					}
				}
				$currentJType = com_wiris_util_json_parser_JType::$TYPE_HEURISTIC;
				$expectingColon = true;
				$i++;
				unset($e2);
			} else {
				if($currentJType === com_wiris_util_json_parser_JType::$TYPE_STRING) {
					try {
						$extracted = com_wiris_util_json_parser_JsonParse::extractString($jsonString, $i, $singleQuoteString);
						$i = $extracted->sourceEnd;
						$value = $extracted->str;
						$singleQuoteString = false;
						unset($extracted);
					}catch(Exception $»e) {
						$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
						$e2 = $_ex_;
						{
							throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "String did not have ending quote"), null));
						}
					}
					if($currentContainer === null) {
						return $value;
					} else {
						$expectingComma = true;
						if(com_wiris_system_TypeTools::isHash($currentContainer)) {
							_hx_deref(($currentContainer))->set($propertyName, $value);
							$currentJType = com_wiris_util_json_parser_JType::$TYPE_OBJECT;
						} else {
							$currentContainer->push($value);
							$currentJType = com_wiris_util_json_parser_JType::$TYPE_ARRAY;
						}
					}
					$i++;
					unset($e2);
				} else {
					if($currentJType === com_wiris_util_json_parser_JType::$TYPE_NUMBER) {
						$withDecimal = false;
						$withE = false;
						do {
							$current = com_wiris_system_Utf8::charValueAt($jsonString, $i);
							if(!$withDecimal && $current === 46) {
								$withDecimal = true;
							} else {
								if(!$withE && ($current === 101 || $current === 69)) {
									$withE = true;
								} else {
									if(!com_wiris_util_json_parser_JsonParse::isNumberStart($current) && $current !== 43) {
										break;
									}
								}
							}
						} while($i++ < $end);
						$valueString = com_wiris_system_Utf8::mbSubstring($jsonString, $fieldStart, $i - $fieldStart);
						try {
							if($withDecimal || $withE) {
								$value = Std::parseFloat($valueString);
							} else {
								$value = Std::parseInt($valueString);
							}
						}catch(Exception $»e) {
							$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
							$e2 = $_ex_;
							{
								throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "\"" . $valueString . "\" expected to be a number, but wasn't"), null));
							}
						}
						if($currentContainer === null) {
							return $value;
						} else {
							$expectingComma = true;
							if(com_wiris_system_TypeTools::isHash($currentContainer)) {
								_hx_deref(($currentContainer))->set($propertyName, $value);
								$currentJType = com_wiris_util_json_parser_JType::$TYPE_OBJECT;
							} else {
								$currentContainer->push($value);
								$currentJType = com_wiris_util_json_parser_JType::$TYPE_ARRAY;
							}
						}
						unset($withE,$withDecimal,$valueString,$e2);
					} else {
						if($currentJType === com_wiris_util_json_parser_JType::$TYPE_CONSTANT) {
							while(com_wiris_util_json_parser_JsonParse::isLetter($current) && $i++ < $end) {
								$current = com_wiris_system_Utf8::charValueAt($jsonString, $i);
							}
							$valueString = com_wiris_system_Utf8::mbSubstring($jsonString, $fieldStart, $i - $fieldStart);
							if("false" === $valueString) {
								$value = false;
							} else {
								if("true" === $valueString) {
									$value = true;
								} else {
									if("null" === $valueString) {
										$value = null;
									} else {
										if(com_wiris_system_TypeTools::isHash($currentContainer)) {
											$stateStack->push(new com_wiris_util_json_parser_State($propertyName, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_OBJECT));
										} else {
											if(com_wiris_system_TypeTools::isArray($currentContainer)) {
												$stateStack->push(new com_wiris_util_json_parser_State($propertyName, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_ARRAY));
											}
										}
										throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "\"" . $valueString . "\" is not a valid constant. Missing quotes?"), null));
									}
								}
							}
							if($currentContainer === null) {
								return $value;
							} else {
								$expectingComma = true;
								if(com_wiris_system_TypeTools::isHash($currentContainer)) {
									_hx_deref(($currentContainer))->set($propertyName, $value);
									$currentJType = com_wiris_util_json_parser_JType::$TYPE_OBJECT;
								} else {
									$currentContainer->push($value);
									$currentJType = com_wiris_util_json_parser_JType::$TYPE_ARRAY;
								}
							}
							unset($valueString);
						} else {
							if($currentJType === com_wiris_util_json_parser_JType::$TYPE_HEURISTIC) {
								while(com_wiris_util_json_parser_JsonParse::isWhitespace($current) && $i++ < $end) {
									$current = com_wiris_system_Utf8::charValueAt($jsonString, $i);
								}
								if($current !== 58 && $expectingColon) {
									$stateStack->push(new com_wiris_util_json_parser_State($propertyName, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_OBJECT));
									throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "wasn't followed by a colon"), null));
								}
								if($current === 58) {
									if($expectingColon) {
										$expectingColon = false;
										$i++;
									} else {
										$stateStack->push(new com_wiris_util_json_parser_State($propertyName, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_OBJECT));
										throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "was followed by too many colons"), null));
									}
								} else {
									if($current === 34 || com_wiris_util_json_parser_JsonParse::$ALLOW_SINGLE_QUOTES && $current === 39) {
										$currentJType = com_wiris_util_json_parser_JType::$TYPE_STRING;
										$singleQuoteString = $current === 39;
										$fieldStart = $i;
									} else {
										if($current === 123) {
											$stateStack->push(new com_wiris_util_json_parser_State($propertyName, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_OBJECT));
											$currentJType = com_wiris_util_json_parser_JType::$TYPE_OBJECT;
											$currentContainer = new Hash();
											$i++;
										} else {
											if($current === 91) {
												$stateStack->push(new com_wiris_util_json_parser_State($propertyName, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_OBJECT));
												$currentJType = com_wiris_util_json_parser_JType::$TYPE_ARRAY;
												$currentContainer = new _hx_array(array());
												$i++;
											} else {
												if(com_wiris_util_json_parser_JsonParse::isLetter($current)) {
													$currentJType = com_wiris_util_json_parser_JType::$TYPE_CONSTANT;
													$fieldStart = $i;
												} else {
													if(com_wiris_util_json_parser_JsonParse::isNumberStart($current)) {
														$currentJType = com_wiris_util_json_parser_JType::$TYPE_NUMBER;
														$fieldStart = $i;
													} else {
														throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "unexpected character \"" . _hx_string_rec($current, "") . "\" instead of object value"), null));
													}
												}
											}
										}
									}
								}
							} else {
								if($currentJType === com_wiris_util_json_parser_JType::$TYPE_OBJECT) {
									while(com_wiris_util_json_parser_JsonParse::isWhitespace($current) && $i++ < $end) {
										$current = com_wiris_system_Utf8::charValueAt($jsonString, $i);
									}
									if($current === 44) {
										if($expectingComma) {
											$expectingComma = false;
											$i++;
										} else {
											$stateStack->push(new com_wiris_util_json_parser_State($propertyName, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_OBJECT));
											throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "followed by too many commas"), null));
										}
									} else {
										if($current === 34 || com_wiris_util_json_parser_JsonParse::$ALLOW_SINGLE_QUOTES && $current === 39) {
											if($expectingComma) {
												$stateStack->push(new com_wiris_util_json_parser_State($propertyName, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_OBJECT));
												throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "wasn't followed by a comma"), null));
											}
											$currentJType = com_wiris_util_json_parser_JType::$TYPE_NAME;
											$singleQuoteString = $current === 39;
											$fieldStart = $i;
										} else {
											if($current === 125) {
												if($stateStack->length > 0) {
													$upper = $stateStack->pop();
													$upperContainer = $upper->container;
													$parentName = $upper->propertyName;
													$currentJType = $upper->type;
													if(com_wiris_system_TypeTools::isHash($upperContainer)) {
														_hx_deref(($upperContainer))->set($parentName, $currentContainer);
													} else {
														$upperContainer->push($currentContainer);
													}
													$currentContainer = $upperContainer;
													$expectingComma = true;
													$i++;
													unset($upperContainer,$upper,$parentName);
												} else {
													return $currentContainer;
												}
											} else {
												if(!com_wiris_util_json_parser_JsonParse::isWhitespace($current)) {
													throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "unexpected character '" . _hx_string_rec($current, "") . "' where a property name is expected. Missing quotes?"), null));
												}
											}
										}
									}
								} else {
									if($currentJType === com_wiris_util_json_parser_JType::$TYPE_ARRAY) {
										while(com_wiris_util_json_parser_JsonParse::isWhitespace($current) && $i++ < $end) {
											$current = com_wiris_system_Utf8::charValueAt($jsonString, $i);
										}
										if($current !== 44 && $current !== 93 && $current !== 125 && $expectingComma) {
											$stateStack->push(new com_wiris_util_json_parser_State(null, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_ARRAY));
											throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "wasn't preceded by a comma"), null));
										}
										if($current === 44) {
											if($expectingComma) {
												$expectingComma = false;
												$i++;
											} else {
												$stateStack->push(new com_wiris_util_json_parser_State(null, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_ARRAY));
												throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "preceded by too many commas"), null));
											}
										} else {
											if($current === 34 || com_wiris_util_json_parser_JsonParse::$ALLOW_SINGLE_QUOTES && $current === 39) {
												$currentJType = com_wiris_util_json_parser_JType::$TYPE_STRING;
												$singleQuoteString = $current === 39;
												$fieldStart = $i;
											} else {
												if($current === 123) {
													$stateStack->push(new com_wiris_util_json_parser_State(null, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_ARRAY));
													$currentJType = com_wiris_util_json_parser_JType::$TYPE_OBJECT;
													$currentContainer = new Hash();
													$i++;
												} else {
													if($current === 91) {
														$stateStack->push(new com_wiris_util_json_parser_State(null, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_ARRAY));
														$currentJType = com_wiris_util_json_parser_JType::$TYPE_ARRAY;
														$currentContainer = new _hx_array(array());
														$i++;
													} else {
														if($current === 93) {
															if($stateStack->length > 0) {
																$upper = $stateStack->pop();
																$upperContainer = $upper->container;
																$parentName = $upper->propertyName;
																$currentJType = $upper->type;
																if(com_wiris_system_TypeTools::isHash($upperContainer)) {
																	_hx_deref(($upperContainer))->set($parentName, $currentContainer);
																} else {
																	$upperContainer->push($currentContainer);
																}
																$currentContainer = $upperContainer;
																$expectingComma = true;
																$i++;
																unset($upperContainer,$upper,$parentName);
															} else {
																return $currentContainer;
															}
														} else {
															if(com_wiris_util_json_parser_JsonParse::isLetter($current)) {
																$currentJType = com_wiris_util_json_parser_JType::$TYPE_CONSTANT;
																$fieldStart = $i;
															} else {
																if(com_wiris_util_json_parser_JsonParse::isNumberStart($current)) {
																	$currentJType = com_wiris_util_json_parser_JType::$TYPE_NUMBER;
																	$fieldStart = $i;
																} else {
																	$stateStack->push(new com_wiris_util_json_parser_State($propertyName, $currentContainer, com_wiris_util_json_parser_JType::$TYPE_ARRAY));
																	throw new HException(new com_wiris_system_Exception(com_wiris_util_json_parser_JsonParse::buildErrorMessage($stateStack, "Unexpected character \"" . _hx_string_rec($current, "") . "\" instead of array value"), null));
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		throw new HException(new com_wiris_system_Exception("Root element wasn't terminated correctly (Missing ']' or '}'?)", null));
	}
	static function extractString($jsonString, $fieldStart, $singleQuote) {
		$builder = new StringBuf();
		$ret = null;
		while(true) {
			$i = com_wiris_util_json_parser_JsonParse::indexOfSpecial($jsonString, $fieldStart, $singleQuote);
			$c = com_wiris_system_Utf8::charValueAt($jsonString, $i);
			if(!$singleQuote && $c === 34 || $singleQuote && $c === 39) {
				$builder->add(com_wiris_system_Utf8::mbSubstring($jsonString, $fieldStart + 1, $i - $fieldStart - 1));
				$ret = new com_wiris_util_json_parser_ExtractedString($i, $builder->b);
				break;
			} else {
				if($c === 92) {
					$builder->add(com_wiris_system_Utf8::mbSubstring($jsonString, $fieldStart + 1, $i - $fieldStart - 1));
					$c = com_wiris_system_Utf8::charValueAt($jsonString, $i + 1);
					if($c === 34) {
						$builder->b .= chr(34);
					} else {
						if($c === 92) {
							$builder->b .= chr(92);
						} else {
							if($c === 47) {
								$builder->b .= chr(47);
							} else {
								if($c === 110) {
									$builder->b .= chr(10);
								} else {
									if($c === 114) {
										$builder->b .= chr(13);
									} else {
										if($c === 116) {
											$builder->b .= chr(9);
										} else {
											if($c === 117) {
												$builder->add(com_wiris_util_json_parser_JsonParse_0($builder, $c, $fieldStart, $i, $jsonString, $ret, $singleQuote));
												$fieldStart = $i + 5;
												continue;
											}
										}
									}
								}
							}
						}
					}
					$fieldStart = $i + 1;
				} else {
					throw new HException("Index out of bounds");
				}
			}
			unset($i,$c);
		}
		return $ret;
	}
	static function indexOfSpecial($str, $start, $singleQuote) {
		$i = $start;
		while(++$i < strlen($str)) {
			$c = com_wiris_system_Utf8::charValueAt($str, $i);
			if(!$singleQuote && $c === 34 || com_wiris_util_json_parser_JsonParse::$ALLOW_SINGLE_QUOTES && $singleQuote && $c === 39 || $c === 92) {
				break;
			}
			unset($c);
		}
		return $i;
	}
	static function isWhitespace($c) {
		return $c === 32 || $c === 9 || $c === 10 || $c === 13;
	}
	static function isLetter($c) {
		return $c >= 97 && $c <= 122;
	}
	static function isNumberStart($c) {
		return $c >= 48 && $c <= 57 || $c === 45;
	}
	static function buildErrorMessage($stateStack, $message) {
		$jsonTrace = "";
		$i = null;
		{
			$_g1 = 0; $_g = $stateStack->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$name = _hx_array_get($stateStack, $i1)->propertyName;
				if($name === null) {
					$list = _hx_array_get($stateStack, $i1)->container;
					$name = "[" . _hx_string_rec($list->length, "") . "]";
					unset($list);
				}
				$jsonTrace .= $name . ((($i1 !== $stateStack->length - 1) ? "." : ""));
				unset($name,$i1);
			}
		}
		$jsonTrace = com_wiris_util_json_parser_JsonParse_1($i, $jsonTrace, $message, $stateStack);
		return $jsonTrace . ": " . $message;
	}
	function __toString() { return 'com.wiris.util.json.parser.JsonParse'; }
}
function com_wiris_util_json_parser_JsonParse_0(&$builder, &$c, &$fieldStart, &$i, &$jsonString, &$ret, &$singleQuote) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(Std::parseInt("0x" . com_wiris_system_Utf8::mbSubstring($jsonString, $i + 2, 4)));
		return $s->toString();
	}
}
function com_wiris_util_json_parser_JsonParse_1(&$i, &$jsonTrace, &$message, &$stateStack) {
	if($jsonTrace === "") {
		return "<root>";
	} else {
		return "<root>." . $jsonTrace;
	}
}
